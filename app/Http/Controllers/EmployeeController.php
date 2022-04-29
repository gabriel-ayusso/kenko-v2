<?php

namespace App\Http\Controllers;

use App\Events\BookingCreated;
use App\Mail\BookingSuccess;
use App\Models\AccountCycle;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class EmployeeController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Employee::class, 'employee');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::orderBy('firstname')->get();
        return view('employees.index', ['employees' => $employees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::with(['category' => function ($q) {
            $q->orderBy('name');
        }])->get();

        return view('employees.create', ['services' => $services]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        $employee = Employee::create($request->all());

        if ($request->input('services')) {
            foreach ($request->input('services') as $id) {
                $employee->services()->attach($id);
            }
        }

        session()->flash('success', "Funcionário salvo com sucesso.");
        return redirect()->route('employees.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return view('employees.show', ['employee' => $employee]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        $services = Service::with(['category' => function ($q) {
            $q->orderBy('name');
        }])->get();
        $users = User::orderBy('name')->get();

        return view('employees.edit', ['employee' => $employee, 'services' => $services, 'users' => $users]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        $employee->firstname = $request->input('firstname');
        $employee->lastname = $request->input('lastname');
        $employee->title = $request->input('title');
        $employee->description = $request->input('description');

        $employee->save();

        $employee->services()->detach(); // remove todos
        if ($request->input('services')) {
            foreach ($request->input('services') as $id) {
                $employee->services()->attach($id);
            }
        }

        session()->flash('success', "Funcionário alterado com sucesso.");
        if ($request->user()->admin)
            return redirect()->route('employees.index');
        else
            return redirect()->route('employees.welcome');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->services()->detach();
        $employee->delete();
        session()->flash('success', "Funcionário excluído com sucesso.");
        return redirect()->route('employees.index');
    }


    /**
     * Store/overwrite the user's avatar
     */
    public function storeAvatar(Request $request, Employee $employee)
    {
        $request->validate([
            'avatar' => 'required|image|max:5120',
        ]);

        $img = Image::make($request->file('avatar'));

        Storage::put("employee_{$employee->id}/avatar.png", $img->encode('png'));

        return redirect()->route('employees.edit', ['employee' => $employee]);
    }

    public function getAvatar(Employee $employee)
    {
        if (Storage::exists("employee_{$employee->id}/avatar.png"))
            return Storage::download("employee_{$employee->id}/avatar.png");
        else
            return Storage::download('public/img/user.png');
    }

    public function welcome(Request $request)
    {
        $employee = $request->user()->employee;
        $cycle = AccountCycle::current();
        return view('employees.welcome', ['employee' => $employee, 'cycle' => $cycle]);
    }

    public function specialConditions(Employee $employee)
    {
        return view('employees.special-conditions', ['employee' => $employee]);
    }

    public function specialConditionsUpdate(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_id' => ['required', 'numeric', 'exists:employees,id'],
            'service_id' => ['required', 'numeric', 'exists:services,id'],
            'comission' => ['required', 'numeric', 'min:0'],
        ]);

        DB::table('employee_service')->where([
            'employee_id' => $request->input('employee_id'),
            'service_id' => $request->input('service_id')
        ])->update(['comission' => $request->input('comission')]);

        session()->flash('success', "Registro alterado com sucesso.");
        return redirect()->route('employees.edit', ['employee' => $employee]);
    }

    public function booking(Request $request, Employee $employee)
    {
        return view('employees.booking', ['employee' => $employee]);
    }

    public function bookingStore(Request $request, Employee $employee)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date', 'after_or_equal:today'],
            'service_id' => ['required', 'numeric', 'exists:services,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:18'],
        ]);

        $service = Service::find($request->input('service_id'));
        $date = Carbon::parse($request->input('date'));
        $time = Carbon::parse($request->input('time'));
        $dateTime = Carbon::create($date->year, $date->month, $date->day, $time->hour, $time->minute, $time->second);

        if (!$employee->isAvailableOn($dateTime, $service->time)) {
            session()->flash('danger', "Esse horário não está disponível para agendamento. Selecione outro e tente novamente");
            $request->flash();
            return view('employees.booking', ['employee' => $employee]);
        }

        $cpf = str_replace('-', '', str_replace('.', '', $request->input('cpf')));

        $booking = Booking::create([
            'cpf' => $cpf,
            'name' => $request->input('name'),
            'email' => $request->input('email') ? $request->input('email') : '',
            'phone' => $request->input('phone') ? $request->input('phone') : '',
            'service_id' => $request->input('service_id'),
            'employee_id' => $employee->id,
            'date' => $dateTime,
            'duration' => $service->time,
            'status' => 'A',
            'confirmation_hash' => Str::uuid()->toString(),
            'ip' => $request->ip(),
        ]);

        $booking->addComission();

        event(new BookingCreated($booking));

        // if($booking->email)
        //     Mail::to($booking->email)->send(new BookingSuccess($booking));
        //Mail::to($booking->email)->send(new BookingSuccess($booking));

        session()->flash('success', "Agendamento realizado com sucesso.");
        return redirect()->route('employees.welcome', ['employee' => $employee]);
    }

    public function weekly(Request $request, Employee $employee)
    {
        if (!$request->user()->manager)
            return abort(403);

        // Primeiro dia da semana...
        $start = Carbon::parse('today');
        // while ($start->dayOfWeek != 0) {
        //     $start->addDays(-1);
        // }
        $start->addDays(-1);
        $end = $start->clone()->addDays(7);

        $bookings = Booking::where('employee_id', $employee->id)->where('status', '<>', 'C')->where('date', '>=', $start)->where('date', '<=', $end)->orderBy('date')->get();
        $minmax = DB::select(sprintf("select min(start) min, max(end) max from employee_availabilities where employee_id = %d;", $employee->id));

        return view('employees.weekly', ['start' => $start, 'bookings' => $bookings, 'minmax' => $minmax[0]]);
    }
}
