<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeAvailabilityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EmployeeAvailability::class, 'availability');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Employee $employee)
    {
        $user = $request->user();
        if(!$user->admin && $user->employee_id != $employee->id)
            return abort(403);

        $availabilities = EmployeeAvailability::where('employee_id', $employee->id)
            ->orderBy('weekday')
            ->orderBy('start')->get();

        return view('employee-availabilities.index', ['employee' => $employee, 'availabilities' => $availabilities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Employee $employee)
    {
        return view('employee-availabilities.create', ['employee' => $employee]);
    }

    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'weekday' => ['required', 'numeric', 'min:1', 'max:7'],
            'start' => ['required', 'regex:/^((?:[01]\d|2[0-3]):[0-5]\d)/'],
            'end' => ['required', 'regex:/^((?:[01]\d|2[0-3]):[0-5]\d)/'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Employee $employee)
    {
        $this->validator($request)->validate();

        $start = Carbon::parse('1970-01-01 ' . $request->input('start'));
        $end = Carbon::parse('1970-01-01 ' . $request->input('end'));

        EmployeeAvailability::create([
            'employee_id' => $employee->id,
            'weekday'  => $request->input('weekday'),
            'start' => $start,
            'end' => $end,
        ]);

        session()->flash('success', "Disponibilidade incluída com sucesso.");
        return redirect()->route('availabilities.index', ['employee' => $employee]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeAvailability  $availability
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeAvailability $availability)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeAvailability  $availability
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee, EmployeeAvailability $availability)
    {
        return view('employee-availabilities.edit', ['employee' => $employee, 'availability' => $availability]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeAvailability  $availability
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee, EmployeeAvailability $availability)
    {
        $this->validator($request)->validate();

        $start = Carbon::parse('1970-01-01 ' . $request->input('start'));
        $end = Carbon::parse('1970-01-01 ' . $request->input('end'));

        $availability->weekday = $request->input('weekday');
        $availability->start = $start;
        $availability->end = $end;
        $availability->save();

        session()->flash('success', "Disponibilidade alterada com sucesso.");
        return redirect()->route('availabilities.index', ['employee' => $employee]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeAvailability  $availability
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee, EmployeeAvailability $availability)
    {
        $availability->delete();
        session()->flash('success', "Disponibilidade excluída com sucesso.");
        return redirect()->route('availabilities.index', ['employee' => $employee]);

    }
}
