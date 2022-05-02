@php
$today = Carbon\Carbon::parse('today');
@endphp
<div class="card">
    <div class="card-header">Próximos serviços</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <tr>
                    <th>Data</th>
                    <th>Nome</th>
                    <th>Serviço</th>
                </tr>
                @foreach($employee->bookings()->where('status', '<>', 'C')->where('date', '>=', $today)->orderBy('date', 'desc')->get() as $booking)
                <tr>
                    <td>
                        <a href="{{route('bookings.show', $booking)}}">{{$booking->date->format('d/m H:i')}}</a>
                        @switch($booking->status)
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
                    </td>
                    <td>{{$booking->name}}</td>
                    <td>{{$booking->service->name}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
