@extends('layouts.app')

@section('content')

<h1>Configurações do Sistema</h1>


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Acertos do Ciclo</div>
            <div class="card-body">
                <h5>Os seguintes registros serão automaticamente ajustados</h5>

                <table class="table table-sm table-striped">
                    <thead>
                        <th>Agend. #</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Profissional</th>
                        <th>Serviço</th>
                        <th>Ciclo #</th>
                        <th>Início Ciclo</th>
                        <th>Fim Ciclo</th>
                    </thead>
                    <tbody>
                        @foreach($wrongTransactions as $trans)
                        <tr>
                            <td>{{$trans->booking_id}}</td>
                            <td>{{date('d/m/Y H:i:s', strtotime($trans->date))}}</td>
                            <td>{{$trans->customer_name}}</td>
                            <td>{{$trans->employee_name}}</td>
                            <td>{{$trans->service}}</td>
                            <td>{{$trans->cycle_id}}</td>
                            <td>{{$trans->cycle_start}}</td>
                            <td>{{$trans->cycle_end}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(count($wrongTransactions) > 0)
                <div class="row">
                    <div class="col text-right">
                        <form method="POST" action="{{url('/configuration/adjustTransactionCycles')}}">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-primary" onclick="return confirm('ATENÇÃO: essa ação não pode ser desfeita. Deseja continuar?')">Ajustar</button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection