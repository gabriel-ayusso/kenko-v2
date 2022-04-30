@extends('layouts.app')

@php
function getObservacoes($booking) {
    $ret = '';

    if (!$booking->caCustomer) {
        $ret .= '<i class="fa fa-circle-info text-info"></i> Um novo <b>cliente</b> será criado';
    }

    if (!$booking->caService) {
        $ret .= (empty($ret) ? '' : '<br/>') . '<i class="fa fa-circle-info text-info"></i> Um novo <b>serviço</b> será criado';
    }

    return $ret;
}

$total = 0;
@endphp

@section('content')
<div>
    <h1>Integração com Conta Azul</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Cliente</th>
                <th>Profissional</th>
                <th>Serviço</th>
                <th class="text-end">Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{$booking->date->format('d/m')}} <strong>{{$booking->date->format('H:i')}}</strong></td>
                <td>
                    @if(!$booking->ca_customer_id)
                    <i class="fa fa-circle-info text-info" data-bs-toggle="tooltip" title="Esse cliente não existe no Conta Azul"></i>
                    @endif
                    {{$booking->name}}
                    @if($booking->phone) <small class="text-muted badge bg-light">{{$booking->phone}}</small> @endif
                    @if($booking->email) <small class="text-muted badge bg-light">{{$booking->email}}</small> @endif
                </td>
                <td>{{$booking->employee->firstname}} {{$booking->employee->lastname}}</td>
                <td>
                    @if(!$booking->ca_service_id)
                    <i class="fa fa-circle-info text-info" data-bs-toggle="tooltip" title="Esse serviço não existe no Conta Azul"></i>
                    @endif
                    {{$booking->service->name}}
                </td>
                <td class="text-end">{{number_format($booking->service->price,2)}}</td>
                <td>@if($booking->status != 'P') <i class="fa fa-triangle-exclamation text-danger" title="Esse agendamento não está pago"></i> @endif {{Helper::bookingStatus($booking->status)}}</td>
            </tr>

            @php
            $total += $booking->service->price;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="4">TOTAL</td>
                <td class="text-end">{{number_format($total, 2)}}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
            <div class="card card-light">
                <div class="card-body">
                    <form method="POST" action="{{route('conta-azul.sync-bookings')}}">
                        @csrf
                        @foreach($bookings as $booking)
                            <input type="hidden" name="id[]" value="{{$booking->id}}">
                        @endforeach
                        <div class="alert alert-warning">
                            <b>ATENÇÃO:</b> Verique os agendamentos acima. O sitema gerará uma venda para cada um deles. <br/>
                            <i class="fa fa-circle-info text-info"></i> Cliente ou serviço <b>não existente</b> no Conta Azul. <br/>
                            <i class="fa fa-triangle-exclamation text-danger"></i> Agendamento não está marcado como <b>Pago</b>. <br/>
                        </div>

                        <div class="mb-3">
                            <label for="bank_id" class="form-label">Selecione a conta:</label>
                            <select class="form-select" name="bank_id" id="bank_id">
                                <option value="">-- selecione --</option>
                                @foreach($banks as $bank)
                                <option value="{{$bank->id}}" {{ $lastBankId == $bank->id ? 'selected' : '' }}>{{$bank->name}}</option>
                                @endforeach
                            </select>
                            @error('bank_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-arrow-right-arrow-left me-1"></i> Enviar para o Conta Azul</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
@endsection
