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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ManagerBookingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Booking::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookings = Booking::orderBy('date', 'desc')->with('employee:id,firstname,lastname')->with('service:id,name')->paginate(25);
        return view('manager.booking.index', ['bookings' => $bookings]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::select('id', 'firstname', 'lastname')->orderBy('firstname')->get();
        $services = DB::select("select a.id, a.name, b.name category from services a inner join service_categories b on a.category_id = b.id order by b.name, a.name");
        return view('manager.booking.create', ['employees' => $employees, 'services' => $services]);
    }

    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'service_id' => ['required', 'exists:services,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:A,E,P,C'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request)->validate();

        $service = Service::find($request->input('service_id'));
        $employee = Employee::find($request->input('employee_id'));
        $date = Carbon::parse($request->input('date'));

        if ($request->boolean('checkAvailability') && !$employee->isAvailableOn($date, $service->time)) {
            $request->flash();
            session()->flash('danger', "Este horário não está disponível para esse profissional.");
            $employees = Employee::select('id', 'firstname', 'lastname')->orderBy('firstname')->get();
            $services = DB::select("select a.id, a.name, b.name category from services a inner join service_categories b on a.category_id = b.id order by b.name, a.name");
            return view('manager.booking.create', ['employees' => $employees, 'services' => $services]);
        }

        $booking = Booking::create([
            'cpf' => '',
            'name' => $request->input('name'),
            'email' => $request->input('email') ?? '',
            'phone' => $request->input('phone') ?? '',
            'service_id' => $request->input('service_id'),
            'employee_id' => $request->input('employee_id'),
            'date' => $date,
            'duration' => $service->time,
            'status' => $request->input('status'),
            'confirmation_hash' => Str::uuid()->toString(),
            'ip' => $request->ip(),
            'comments' => $request->input('comments'),
        ]);
        if ($request->boolean('applyComission')) {
            $booking->addComission();
        }

        if ($request->boolean('sendEmail')) {
            event(new BookingCreated($booking));
            // $admins = User::where('admin', true)->get();
            // if ($booking->email)
            //     Mail::to($booking->email)->bcc($admins)->send(new BookingSuccess($booking));
            // else
            //     Mail::to($admins)->send(new BookingSuccess($booking));
        }

        session()->flash('success', "Agendamento realizado com sucesso.");
        return redirect()->route('booking.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        $employees = Employee::select('id', 'firstname', 'lastname')->orderBy('firstname')->get();
        $services = DB::select("select a.id, a.name, b.name category from services a inner join service_categories b on a.category_id = b.id order by b.name, a.name");
        return view('manager.booking.edit', ['booking' => $booking, 'employees' => $employees, 'services' => $services]);
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
        $this->validator($request)->validate();
        $user = $request->user();
        Log::info("Usuário $user->email alterou o agendamento $booking->id");

        $service = Service::find($request->input('service_id'));

        $booking->name = $request->input('name');
        $booking->email = $request->input('email') ?? '';
        $booking->phone = $request->input('phone') ?? '';
        $booking->date = Carbon::parse($request->input('date'));
        $booking->service_id = $request->input('service_id');
        $booking->employee_id = $request->input('employee_id');
        $booking->comments = $request->input('comments');
        $booking->status = $request->input('status');
        $booking->duration = $service->time;
                $booking->save();

        if ($request->boolean('recalculate')) {
            $current_cycle = AccountCycle::current();
            $transaction = $booking->transactions()->first();
            if($transaction && $transaction->account_cycle_id < $current_cycle->id){
                $request->flash();
                session()->flash('danger', "Não é possível alterar a comissão para ciclos passados.");
                return $this->edit($booking);
            }
            $booking->recalculateComission();
        }

        session()->flash('success', "Agendamento alterado com sucesso.");
        return redirect()->route('booking.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        session()->flash('success', "Agendamento excluído com sucesso.");
        return redirect()->route('booking.index');
    }
}
