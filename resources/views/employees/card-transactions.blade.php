@php

$query = $employee->transactions()->whereHas('booking', function($q) {
    $q->where('status', 'P');
});

if($cycle)
    $sumPaid = $query->where('account_cycle_id', $cycle->id)->sum('amount');
else
    $sumPaid = $query->whereNull('account_cycle_id')->sum('amount');

$query = $employee->transactions()->whereHas('booking', function($q) {
    $q->where('status', '<>', 'P')->where('status', '<>', 'C');
});

if($cycle)
    $sumNoPaid = $query->where('account_cycle_id', $cycle->id)->sum('amount');
else
    $sumNoPaid = $query->whereNull('account_cycle_id')->sum('amount');

if(!function_exists('getCycleText')) {
    function getCycleText($cycle) {
        $text = $cycle->start->format('d/m/Y');
        if($cycle->end) {
            $text .= "-" . $cycle->end->format('d/m/Y');
        }
        return $text;
    }
}

@endphp

<div class="card">
    <div class="card-header">Transações <span class="text-primary text-end float-right">Ciclo <strong>{{isset($cycle) ? getCycleText($cycle) : '(não definido)'}}</strong></span></div>
    <div class="card-body">
        <h5>
            <span class="text-primary">Saldo: R$ {{number_format($sumPaid,2)}}</span>
            <span class="text-warning ml-md-3">Vinculado: R$ {{number_format($sumNoPaid,2)}}</span>
        </h5>
        <div class="table-responsive">
        <table class="table table-sm table-hover">
            <tr>
                <th>Data</th>
                <th>Valor</th>
                <th>Descrição</th>
            </tr>
            @foreach($employee->transactions()->where('account_cycle_id', isset($cycle) ? $cycle->id : null)->orderBy('date', 'desc')->get() as $transaction)
            @if(!$transaction->booking || $transaction->booking->status !== 'C')
            <tr>
                <td>{{$transaction->date->format('d/m')}}</td>
                <td>{{number_format($transaction->amount, 2)}}</td>
                <td class="text-muted text-truncate">
                    @if($transaction->booking)
                    @switch($transaction->booking->status)
                        @case('A')
                            <span class="badge bg-primary">agendado</span>
                            @break
                        @case('E')
                            <span class="badge bg-warning">executado</span>
                            @break
                        @case('P')
                            <span class="badge bg-success">pago</span>
                            @break
                        @case('C')
                            <span class="badge bg-danger">cancelado</span>
                            @break
                        @default
                            @break
                    @endswitch
                    @endif
                    {{$transaction->description}}
                </td>
            </tr>
            @endif
            @endforeach
        </table>
        </div>
    </div>
</div>
