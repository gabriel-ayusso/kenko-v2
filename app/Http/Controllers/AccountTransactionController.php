<?php

namespace App\Http\Controllers;

use App\Models\AccountCycle;
use App\Models\AccountTransaction;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AccountTransactionController extends Controller
{
    function __construct()
    {
        $this->authorizeResource(AccountTransaction::class, 'transaction');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = AccountTransaction::orderBy('date', 'desc')->with('employee:id,firstname,lastname')->paginate(15);
        return view('transactions.index', ['transactions' => $transactions]);
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
        $cycles = AccountCycle::orderBy('start', 'desc')->get();

        return view('transactions.create', [
            'employees' => $employees,
            'services' => $services,
            'cycles' => $cycles
        ]);
    }

    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'employee_id' => ['required', 'exists:employees,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:255'],
            'account_cycle_id' => ['required', 'exists:account_cycles,id'],
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

        $date = Carbon::parse($request->input('date'));

        $transaction = AccountTransaction::create([
            'date' => $date,
            'employee_id' => $request->input('employee_id'),
            'service_id' => $request->input('service_id'),
            'booking_id' => $request->input('booking_id'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'account_cycle_id' => $request->input('account_cycle_id'),
        ]);

        session()->flash('success', "Registro incluído com sucesso.");
        return redirect()->route('transactions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(AccountTransaction $accountTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AccountTransaction  $accountTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(AccountTransaction $transaction)
    {
        $employees = Employee::select('id', 'firstname', 'lastname')->orderBy('firstname')->get();
        $services = DB::select("select a.id, a.name, b.name category from services a inner join service_categories b on a.category_id = b.id order by b.name, a.name");
        $cycles = AccountCycle::orderBy('start', 'desc')->get();
        return view('transactions.edit', [
            'transaction' => $transaction,
            'employees' => $employees,
            'services' => $services,
            'cycles' => $cycles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AccountTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccountTransaction $transaction)
    {
        $this->validator($request)->validate();

        $transaction->date = Carbon::parse($request->input('date'));
        $transaction->employee_id = $request->input('employee_id');
        $transaction->service_id = $request->input('service_id');
        $transaction->booking_id = $request->input('booking_id');
        $transaction->amount = $request->input('amount');
        $transaction->description = $request->input('description');
        $transaction->account_cycle_id = $request->input('account_cycle_id');
        $transaction->save();

        session()->flash('success', "Registro alterado com sucesso.");
        return redirect()->route('transactions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountTransaction $transaction)
    {
        $transaction->delete();
        session()->flash('success', "Registro excluído com sucesso.");
        return redirect()->route('transactions.index');
    }
}
