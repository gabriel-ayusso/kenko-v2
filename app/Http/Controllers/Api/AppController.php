<?php

namespace App\Http\Controllers\Api;

use App\Events\BookingCreated;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AppController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'max:255', 'min:6']
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'facebook_token' => '',
            'facebook_avatar' => '',
            'admin' => false,
            'manager' => false,
            'active' => true,
            'origin' => 'app',
            'agenda' => 0,
            'type' => 'C',
            'platform' => $request->platform
        ]);
    }

    public function bookings(Request $request)
    {
        $today = strtotime('today');
        $user = $request->user();

        $bookings = Booking::with(['service', 'employee'])->whereIn('status', ['A', 'P'])->where('date', '>=', $today)->where('user_id', $user->id)->orderBy('date')->get();

        return $bookings;
    }

    public function services()
    {
        $services = Service::orderBy('category.order')->where('private', false)->with('category')->get();
        return response()->json(['result' => 'success', 'services' => $services]);
    }


    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'date' => 'date|required',
            'service_id' => 'required',
            'employee_id' => 'required',
        ]);

        $service = Service::find($request->input('service_id'));
        $employee = Employee::find($request->input('employee_id'));
        $date = Carbon::parse($request->input('date'));

        if (!$employee->isAvailableOn($date, $service->time)) {
            return response()->json(['result' => 'error', 'message' => "Esse horário não está mais disponível."]);
        }

        $booking = Booking::create([
            'cpf' => '',
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $request->input('phone') ? $request->input('phone') : '',
            'service_id' => $request->input('service_id'),
            'employee_id' => $request->input('employee_id'),
            'date' => $request->input('date'),
            'duration' => $service->time,
            'status' => 'A',
            'confirmation_hash' => Str::uuid()->toString(),
            'ip' => $request->ip(),
            'user_id' => $user->id,
        ]);

        Log::debug($booking);

        $booking->addComission();

        event(new BookingCreated($booking));

        return response()->json(['result' => 'success', 'booking' => $booking]);
    }
}
