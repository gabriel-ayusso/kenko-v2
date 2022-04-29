<?php

namespace App\Http\Controllers;

use App\Models\AccountCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountCycleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cycles = AccountCycle::orderBy('start', 'desc')->paginate(15);
        return view('cycles.index', ['cycles' => $cycles]);;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cycles.create');
    }

    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'start' => ['required', 'date'],
            'start' => ['nullable', 'date'],
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

        AccountCycle::create($request->all());

        session()->flash('success', "Registro incluído com sucesso.");
        return redirect()->route('cycles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountCycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function show(AccountCycle $cycle)
    {
        return view('cycles.show', ['cycle' => $cycle]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AccountCycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function edit(AccountCycle $cycle)
    {
        return view('cycles.edit', ['cycle' => $cycle]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AccountCycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccountCycle $cycle)
    {
        $this->validator($request)->validate();

        $cycle->start = $request->input('start');
        $cycle->end = $request->input('end');
        $cycle->save();

        session()->flash('success', "Registro alterado com sucesso.");
        return redirect()->route('cycles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountCycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountCycle $cycle)
    {
        $cycle->delete();
        session()->flash('success', "Registro excluído com sucesso.");
        return redirect()->route('cycles.index');
    }
}
