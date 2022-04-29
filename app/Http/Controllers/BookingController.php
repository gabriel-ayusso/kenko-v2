<?php

namespace App\Http\Controllers;

use App\Events\BookingCreated;
use App\Mail\BookingSuccess;
use App\Models\AccountTransaction;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::orderBy('name')->where('private', false)->get();
        return view('bookings.index', ['services' => $services]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $service = Service::find($request->input('service_id'));
        return view('bookings.create', ['service' => $service]);
    }

    public function createStep2(Request $request)
    {
        $request->validate([
            'date' => 'date|required',
            'service_id' => 'required|numeric'
        ]);

        $date = new Carbon($request->input('date'));
        $service = Service::find($request->input('service_id'));
        $employees = $service->employees()
            ->orderByRaw("(select count(1) from bookings where employee_id = employees.id and date between '" . $date->format('Y-m-d') . " 00:00:00' and '" . $date->format('Y-m-d') . " 23:59:59')")
            ->get();

        $currentDate = Carbon::create($date->year, $date->month, $date->day);
        $availabilities = [];

        foreach ($employees as $employee) {
            $employee_availables = $employee->availabeTimes($currentDate, $service->time);
            $availabilities = array_merge($availabilities, $employee_availables);
        }

        return view('bookings.createStep2', ['service' => $service, 'availabilities' => $availabilities, 'date' => $date]);
    }

    public function createStep3(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'time' => 'required',
        ]);


        $service = Service::find($request->input('service_id'));
        $timeArr = explode('_', $request->input('time'));
        $time = $timeArr[0];
        $employeeId = $timeArr[1];

        $employee = Employee::find($employeeId);

        $fulldate = Carbon::parse($time);

        return view('bookings.createStep3', ['service' => $service, 'date' => $fulldate, 'employee' => $employee]);
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
            'date' => 'date|required',
            'service_id' => 'required',
            'employee_id' => 'required',
            'cpf' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        $service = Service::find($request->input('service_id'));
        $employee = Employee::find($request->input('employee_id'));

        $date = Carbon::parse($request->input('date'));

        if (!$employee->isAvailableOn($date, $service->time)) {
            $request->flash();
            session()->flash('danger', "Sentimos muito, mas esse horário não está mais disponível.");
            return view('bookings.createStep3', ['service' => $service, 'date' => $date, 'employee' => $employee]);
        }

        $cpf = str_replace('-', '', str_replace('.', '', $request->input('cpf')));

        $booking = Booking::create([
            'cpf' => $cpf,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
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

        // $admins = User::where('admin', true)->get();
        // Mail::to($booking->email)->bcc($admins)->send(new BookingSuccess($booking));

        session()->flash('success', "Agendamento realizado com sucesso.");
        return redirect()->route('bookings.index');
    }

    public function confirm(Booking $booking, $token)
    {
        $now = Carbon::now();
        $message = '';

        if ($booking->status == 'X') {
            $message = 'Seu agendamento foi cancelado. Por favor, entre em contato para saber mais informações.';
        } else if ($booking->status == 'C') {
            $message = 'Seu agendamento já foi confirmado antes.';
        }

        if ($booking->confirmation_hash != $token) {
            $message = 'Houve um problema com o código de confirmação do seu agendamento. Por favor, entre em contato para saber mais.';
        }

        if ($booking->date < $now) {
            $message = 'Seu agendamento já aconteceu. Por favor, entre em contato para saber mais.';
        }

        if ($message) {
            return view('bookings.confirmation-failure', ['booking' => $booking, 'message' => $message]);
        } else {
            $booking->status = 'C';
            $booking->save();
            return view('bookings.confirmation-success', ['booking' => $booking]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        return view('bookings.show', ['booking' => $booking]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }

    public function myAvailableTime(Request $request, Service $service)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today']
        ]);

        $user = $request->user();
        $date = new Carbon($request->input('date'));
        $currentDate = Carbon::create($date->year, $date->month, $date->day);
        $employee = $user->employee;

        $availabilities = $employee->availabeTimes($currentDate, $service->time);

        return response()->json(['result' => 'success', 'data' => $availabilities]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        if(!$request->user()->manager)
            return abort(403);

        $booking->status = $request->input('status');
        $booking->save();
        session()->flash('success', "Status alterado com sucesso.");
        return redirect()->route('bookings.show', $booking);
    }

    public function guestBooking(Service $service)
    {
        return view('bookings.guest', ['service' => $service]);
    }

    public function guestBookingConfirm(Request $request, Service $service)
    {
        $request->validate([
            'time' => 'date|required',
            'service_id' => 'required',
            'employee_id' => 'required',
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:25',
        ]);

        $service = Service::find($request->input('service_id'));
        $employee = Employee::find($request->input('employee_id'));
        $date = Carbon::parse($request->input('time'));

        if (!$employee->isAvailableOn($date, $service->time)) {
            return response()->json(['result' => 'error', 'message' => "Esse horário não está mais disponível."]);
        }

        $booking = Booking::create([
            'cpf' => '',
            'name' => $request->input('name'),
            'email' => $request->input('email') ? $request->input('email') : '',
            'phone' => $request->input('phone') ? $request->input('phone') : '',
            'service_id' => $request->input('service_id'),
            'employee_id' => $request->input('employee_id'),
            'date' => $date,
            'duration' => $service->time,
            'status' => 'A',
            'confirmation_hash' => Str::uuid()->toString(),
            'ip' => $request->ip(),
        ]);

        $booking->addComission();

        event(new BookingCreated($booking));

        // $admins = User::where('admin', true)->get();
        // if ($booking->email)
        //     Mail::to($booking->email)->bcc($admins)->send(new BookingSuccess($booking));
        // else
        //     Mail::to($admins)->send(new BookingSuccess($booking));

        return view('bookings.guest-success', ['booking' => $booking]);
    }
}
