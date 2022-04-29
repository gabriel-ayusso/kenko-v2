<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfigurationController extends Controller
{
    public function index()
    {
        $wrongTransactions = DB::select("select b.id booking_id, b.name customer_name, concat(e.firstname, ' ', e.lastname) employee_name, s.name service, c.id cycle_id, b.date, c.start cycle_start, c.end cycle_end from 
                account_transactions a
                inner join bookings b on a.booking_id = b.id
                left outer join account_cycles c on a.account_cycle_id = c.id
                inner join services s on b.service_id = s.id
                inner join employees e on b.employee_id = e.id
            where
                (date(b.date) not between c.start and c.end or a.account_cycle_id is null)
                and b.status <> 'C';");

        return view('configuration.index', ['wrongTransactions' => $wrongTransactions]);
    }

    public function adjustTransactionCycles(Request $request)
    {
        $user = $request->user();
        if (!$user->admin)
            abort(403, "Você não está autorizado a executar essa ação.");

        Log::warning("User $user->email ajusted transaction cycles");

        $query = "update account_transactions a
            inner join bookings b on a.booking_id = b.id
            left outer join account_cycles c on a.account_cycle_id = c.id
        set a.account_cycle_id = (select id from account_cycles where date(b.date) between start and end)
        where
            (date(b.date) not between c.start and c.end or a.account_cycle_id is null)
            and b.status <> 'C';";

        $count = DB::update($query);
        Log::info($count);

        session()->flash('success', "Transações ajustadas com sucesso.");
        return redirect()->to('/configuration');
    }
}
