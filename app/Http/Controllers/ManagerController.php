<?php

namespace App\Http\Controllers;

use App\Models\AccountCycle;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Ui\Presets\React;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!$request->user()->manager)
            return abort(403);
        $employees = Employee::orderBy('firstname')->get();

        $cycleId = $request->input('cycle_id');
        $cycle = null;
        if ($cycleId)
            $cycle = AccountCycle::find($cycleId);

        $cycles = AccountCycle::orderBy('start', 'desc')->get();

        return view('manager.dashboard', ['employees' => $employees, 'cycles' => $cycles, 'currentCycle' => $cycle]);
    }

    public function todaySummary(Request $request)
    {
        if (!$request->user()->manager)
            return abort(403);

        $today = Carbon::parse('today');
        $tomorrow = Carbon::parse('tomorrow');

        $bookings = Booking::where('date', '>=', $today)->where('date', '<', $tomorrow)->orderBy('date')->get();
        return view('manager.today-summary', ['bookings' => $bookings]);
    }

    public function tomorrowSummary(Request $request)
    {
        if (!$request->user()->manager)
            return abort(403);

        $tomorrow = Carbon::parse('tomorrow');
        $dayAfter = Carbon::parse('tomorrow')->addDay();

        $bookings = Booking::where('date', '>=', $tomorrow)->where('date', '<', $dayAfter)->orderBy('date')->get();
        return view('manager.tomorrow-summary', ['bookings' => $bookings]);
    }

    public function invoice(Request $request)
    {
        $bookings = Booking::whereIn('id', $request->input('bookings'))->with('transactions')->with('employee')->orderBy('date')->get();
        return view('manager.invoice', ['bookings' => $bookings]);
    }

    public function pay(Request $request, Booking $booking)
    {
        $user = $request->user();

        if (!$user->manager && !$user->agenda)
            return abort(403);

        $booking->status = 'P';
        $booking->save();
        session()->flash('success', "Agendamento marcado como pago.");
        return redirect()->route('manager.weekly');
    }

    public function weekly2(Request $request)
    {
        $user = $request->user();

        if (!$user->manager && !$user->agenda)
            return abort(403);

        // Primeiro dia da semana...
        $start = Carbon::parse('today');
        $start->addDays(-2);
        $end = $start->clone()->addDays(13);

        $query = "select
                e.id employeeId,
                concat(e.firstname, ' ', e.lastname) employeeName,
                b.id bookingId,
                b.date bookingDate,
                b.name customerName,
                b.status bookingStatus,
                s.name serviceName,
                s.price servicePrice,
                c.name categoryName
            from
                employees e
                inner join bookings b on b.employee_id = e.id
                inner join services s on s.id = b.service_id
                inner join service_categories c on c.id = s.category_id
            where
                b.date >= :start
                and b.date <= :end
                and b.status <> 'C'
            order by e.id, b.date;";

        $data = DB::select($query, ['start' => $start, 'end' => $end]);
        $employeeIds = array_unique(array_column($data, 'employeeId'));
        $employees = [];
        foreach ($employeeIds as $employeeId) {
            $employeeData = array_filter($data, function ($item) use ($employeeId) {
                return $item->employeeId == $employeeId;
            });
            $employees[$employeeId] = array_values($employeeData);
        }

        $query = "select
            category,
            date,
            servicePrice,
            100*servicePrice/(select sum(s.price) from bookings b inner join services s on s.id = b.service_id where date(b.date) = t1.date) as percent
        from (
            select
                c.name category,
                date(b.date) date,
                sum(s.price) servicePrice
            from
                bookings b
                inner join services s on s.id = b.service_id
                inner join service_categories c on c.id = s.category_id
            where
                b.date >= :start and b.date <= :end
                and b.status <> 'C'
            group by 1, 2
            order by 1, 2
        ) t1;";

        $categoriesRaw = DB::select($query, ['start' => $start, 'end' => $end]);
        $uniqueCategories = array_unique(array_column($categoriesRaw, 'category'));
        $categories = [];
        foreach ($uniqueCategories as $category) {
            $categoryData = array_filter($categoriesRaw, function ($item) use ($category) {
                return $item->category == $category;
            });
            $categories[$category] = array_values($categoryData);
        }

        return view('manager.weekly2', ['start' => $start, 'data' => $data, 'employees' => $employees, 'categories' => $categories]);
    }

    public function weekly(Request $request)
    {
        $user = $request->user();

        if (!$user->manager && !$user->agenda)
            return abort(403);

        $start = Carbon::parse('today');

        $start->addDays(-2);
        $end = $start->clone()->addDays(13);

        $employees = Employee::whereHas('bookings', function ($query) use ($start, $end) {
            $query->where('status', '<>', 'C');
            $query->where('date', '>=', $start)->where('date', '<=', $end);
            $query->orderBy('date');
        })->with(['bookings' => function ($query) use ($start, $end) {
            $query->where('status', '<>', 'C');
            $query->where('date', '>=', $start)->where('date', '<=', $end);
            $query->orderBy('date');
        }])->with('bookings.service')->orderBy('firstname')->get();

        $categories = ServiceCategory::all();

        $query = "select ifnull(sum(b.price),0) as total from
                    bookings a
                    inner join services b on a.service_id = b.id
                where
                    a.status <> 'C'
                    and date(a.date) = :current
                    and b.category_id = :category";

        foreach ($categories as $category) {

            $current = $start->clone();
            for ($i = 1; $i <= 13; $i++) {
                $category["total_" . $i] = DB::select($query, ['current' => $current, 'category' => $category->id])[0]->total;
                $current = $current->addDay();
            }
        }

        return view('manager.weekly', ['start' => $start, 'employees' => $employees, 'categories' => $categories]);
    }

    public function nextDay(Request $request)
    {
        $user = $request->user();

        if (!$user->manager && !$user->agenda)
            return abort(403);

        $dt = $request->query('dt');

        if ($dt) {
            $dt = Carbon::parse($dt);
        } else {
            $dt = Carbon::parse('today');
            $dt->addDay();
        }

        $query = "select
                b.date,
                b.name,
                b.ca_sale_id,
                concat(e.firstname, ' ', e.lastname) employee,
                s.name service,
                s.price
            from
                bookings b
                inner join employees e on e.id = b.employee_id
                inner join services s on s.id = b.service_id
            where
                date(b.date) = :dt
            order by 1;";

        $services = DB::select($query, ['dt' => $dt]);


        return view('manager.nextday', ['dt' => $dt, 'services' => $services]);
    }

    public function customer(Request $request)
    {
        $query = Booking::whereRaw("1=1");

        $status = $request->get('status');
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $filters = [
            'status' => $status,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ];

        $hasFilter = false;
        if ($status) {
            $query = $query->where('status', $status);
        }

        if ($name) {
            $query = $query->where('name', 'like', "%$name%");
            $hasFilter = true;
        }

        if ($email) {
            $query = $query->where('email', $email);
            $hasFilter = true;
        }

        if ($phone) {
            $query = $query->where('phone', $phone);
            $hasFilter = true;
        }

        if ($hasFilter) {
            $data = $query->orderByDesc('date')->get();
            return view('manager.customer', ['filters' => $filters, 'hasFilter' => $hasFilter, 'data' => $data]);
        } else {
            return view('manager.customer', ['filters' => $filters, 'hasFilter' => $hasFilter]);
        }
    }
}
