<?php

namespace App\Http\Controllers\Api;

use App\Events\BookingCreated;
use App\Http\Controllers\Controller;
use App\Mail\BookingSuccess;
use App\Models\Attack;
use App\Models\Blacklist;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Regex\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    const COOKIE_NAME = '_kkkbl';
    const COOKIE_VALUE = 'true';

    public function available(Request $request, Service $service)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today']
        ]);

        $date = new Carbon($request->input('date'));
        $query = $service->employees()->orderByRaw("(select count(1) from bookings where bookings.employee_id = employees.id and date between '" . $date->format('Y-m-d') . " 00:00:00' and '" . $date->format('Y-m-d') . " 23:59:59')");
        $employees = $query->get();

        $currentDate = Carbon::create($date->year, $date->month, $date->day);
        $availabilities = [];

        foreach ($employees as $employee) {
            $employee_availables = $employee->availabeTimes($currentDate, $service->time);
            $availabilities = array_merge($availabilities, $employee_availables);
        }

        return response()->json(['result' => 'success', 'data' => $availabilities]);
    }

    public function checkBlacklist(Request $request)
    {
        // verifica se o cookie de blacklist existe
        $cookie = $request->cookie(self::COOKIE_NAME);
        $email = $request->input('email');
        $count_bl = Blacklist::where('email', $email)->count();

        if ($cookie) {
            $value = base64_decode($cookie);
            if ($value === self::COOKIE_VALUE) {
                Attack::create([
                    'email' => $email,
                    'name' => $request->input('name'),
                    'phone' => $request->input('phone'),
                    'reason' => 'cookie',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->url(),
                ]);
                Blacklist::create([
                    'email' => $email,
                    'reason' => 'cookie'
                ]);
                return true;
            }
        }

        // verifica se o email está na BlackList
        if ($count_bl > 0) {
            Attack::create([
                'email' => $email,
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'reason' => 'blacklist',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->url(),
            ]);
            return true;
        }

        // não está na blacklist
        return false;
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'date|required',
            'service_id' => 'required',
            'employee_id' => 'required',
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable|numeric',
        ]);

        $service = Service::find($request->input('service_id'));
        $employee = Employee::find($request->input('employee_id'));
        $date = Carbon::parse($request->input('date'));

        if ($this->checkBlacklist($request)) {
            $booking = new Booking([
                'cpf' => '',
                'name' => $request->input('name'),
                'email' => $request->input('email') ? $request->input('email') : '',
                'phone' => $request->input('phone') ? $request->input('phone') : '',
                'service_id' => $request->input('service_id'),
                'employee_id' => $request->input('employee_id'),
                'date' => $request->input('date'),
                'duration' => $service->time,
                'status' => 'A',
                'confirmation_hash' => Str::uuid()->toString(),
                'ip' => $request->ip(),
            ]);
            return response()->json(['result' => 'success', 'booking' => $booking])->cookie(self::COOKIE_NAME, base64_encode(self::COOKIE_VALUE), strtotime('+1 year'));
        }

        if (!$employee->isAvailableOn($date, $service->time)) {
            return response()->json(['result' => 'error', 'message' => "Esse horário não está mais disponível."]);
        }

        //$cpf = str_replace('-', '', str_replace('.', '', $request->input('cpf')));

        $booking = Booking::create([
            'cpf' => '',
            'name' => $request->input('name'),
            'email' => $request->input('email') ? $request->input('email') : '',
            'phone' => $request->input('phone') ? $request->input('phone') : '',
            'service_id' => $request->input('service_id'),
            'employee_id' => $request->input('employee_id'),
            'date' => $request->input('date'),
            'duration' => $service->time,
            'status' => 'A',
            'confirmation_hash' => Str::uuid()->toString(),
            'ip' => $request->ip(),
        ]);

        $booking->addComission();

        event(new BookingCreated($booking));

        return response()->json(['result' => 'success', 'booking' => $booking]);
    }

    public function services()
    {
        $services = Service::orderBy('category.order')->where('private', false)->with('category')->get();
        return response()->json(['result' => 'success', 'services' => $services]);
    }


    public function categories()
    {

        DB::enableQueryLog();
        $categories = ServiceCategory::orderBy('order')->with([
            'services' => function ($query) {
                $query->with('employees')->has('employees')->where('private', false);
            }
        ])->withCount([
            'services' => function ($query) {
                $query->has('employees')->where('private', false);
            }
        ])->get();

        return response()->json(['result' => 'success', 'categories' => $categories]);
    }

    public function search(Request $request)
    {
        $query = Booking::where('status', '<>', 'C')->with('employee:id,firstname,lastname')->with('service:id,name');

        if ($request->input('date')) {
            $start = Carbon::parse($request->input('date'));
            $end = $start->clone()->addDay();
            $query->where('date', '>=', $start)->where('date', '<', $end);
        }

        if ($request->input('customer')) {
            $name = "%" . $request->input('customer') . "%";
            $query->where('name', 'like', $name);
        }

        if ($request->input('service')) {
            $service = "%" . $request->input('service') . "%";
            $query->whereHas('service', function ($query) use ($service) {
                $query->where('name', 'like', $service);
            });
        }

        if ($request->input('employee')) {
            $employee = "%" . $request->input('employee') . "%";
            $query->whereHas('employee', function ($query) use ($employee) {
                $query->where('firstname', 'like', $employee);
            });
        }

        $bookings = $query->orderBy('date', 'desc')->limit(15)->get();

        return response()->json(['result' => 'success', 'bookings' => $bookings]);
    }
}
