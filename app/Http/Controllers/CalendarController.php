<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $calendars = Calendar::where('employee_id', $user->employee_id)->where('start', '>', $now)->orderBy('start')->get();
        $bookings = Booking::where('employee_id', $user->employee_id)->where('date', '>', $now)->orderBy('date')->get();

        return view('calendars.index', ['calendars' => $calendars, 'employee' => $user->employee, 'bookings' => $bookings]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('calendars.create');
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
            'start' => 'required',
            'end' => 'required',
            'description' => 'required'
        ]);

        Calendar::create([
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'description' => $request->input('description'),
            'employee_id' => Auth()->user()->employee_id
        ]);

        session()->flash('success', "Ausência incluída com sucesso.");
        return redirect()->route('calendars.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function show(Calendar $calendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function edit(Calendar $calendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendar $calendar)
    {
        $calendar->delete();
        session()->flash('success', "Ausência excluída com sucesso.");
        return redirect()->route('calendars.index');
    }
}
