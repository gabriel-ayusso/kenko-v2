<?php

namespace App\Http\Controllers;

use App\Models\EmployeeUnavailability;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeUnavailabilityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EmployeeUnavailability::class, 'unavailability');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Employee $employee)
    {
        $user = $request->user();
        if (!$user->admin && $user->employee_id != $employee->id)
            return abort(403);

        $now = Carbon::now();
        $unavailabilities = EmployeeUnavailability::where('employee_id', $employee->id)
            ->where('end', '>=', $now)
            ->orderBy('start')
            ->get();

        return view('employee-unavailabilities.index', ['employee' => $employee, 'unavailabilities' => $unavailabilities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Employee $employee)
    {
        return view('employee-unavailabilities.create', ['employee' => $employee]);
    }

    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'description' => ['required', 'string', 'max:128'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Employee $employee, Request $request)
    {
        $this->validator($request)->validate();

        $start = Carbon::parse($request->input('start'));
        $end = Carbon::parse($request->input('end'));

        EmployeeUnavailability::create([
            'employee_id' => $employee->id,
            'start' => $start,
            'end' => $end,
            'description' => $request->input('description'),
        ]);

        session()->flash('success', "Registro incluído com sucesso.");
        return redirect()->route('unavailabilities.index', ['employee' => $employee]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeUnavailability  $unavailability
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee, EmployeeUnavailability $unavailability)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeUnavailability  $unavailability
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee, EmployeeUnavailability $unavailability)
    {
        return view('employee-unavailabilities.edit', ['employee' => $employee, 'unavailability' => $unavailability]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeUnavailability  $unavailability
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee, EmployeeUnavailability $unavailability)
    {
        $this->validator($request)->validate();

        $unavailability->start = Carbon::parse($request->input('start'));
        $unavailability->end = Carbon::parse($request->input('end'));
        $unavailability->description = $request->input('description');
        $unavailability->save();

        session()->flash('success', "Registro alterado com sucesso.");
        return redirect()->route('unavailabilities.index', ['employee' => $employee]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeUnavailability  $unavailability
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee, EmployeeUnavailability $unavailability)
    {
        $unavailability->delete();
        session()->flash('success', "Registro excluído com sucesso.");
        return redirect()->route('unavailabilities.index', ['employee' => $employee]);
    }
}
