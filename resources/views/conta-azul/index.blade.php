@extends('layouts.app')

@section('content')
<div class="ca-index">
    <h1>Integração Conta Azul</h1>
    @if (isset($message))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation fa-lg text-warning"></i>
        {{$message}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    <hr />
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3" style="max-width: 18rem;">
                <div class="card-header fw-5"><i class="fa-solid fa-spa fa-lg me-1"></i>Serviços</div>
                <div class="card-body text-center">
                    <h5 class="card-title display-3 fw-bold">{{$countServices}}</h5>
                    <div class="d-grid gap-2">
                        <a href="/conta-azul/import-services" class="btn btn-outline-light btn-lg">Importar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3" style="max-width: 18rem;">
                <div class="card-header fw-5"><i class="fa-solid fa-person fa-lg me-1"></i> Clientes</div>
                <div class="card-body text-center">
                    <h5 class="card-title display-3 fw-bold">{{$countCustomers}}</h5>
                    <div class="d-grid gap-2">
                        <a href="/conta-azul/import-customers" class="btn btn-outline-light btn-lg">Importar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3" style="max-width: 18rem;">
                <div class="card-header fw-5"><i class="fa-solid fa-building-columns fa-lg me-1"></i> Bancos</div>
                <div class="card-body text-center">
                    <h5 class="card-title display-3 fw-bold">{{$countBanks}}</h5>
                    <div class="d-grid gap-2">
                        <a href="/conta-azul/import-banks" class="btn btn-outline-light btn-lg">Importar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr />
    <h2>Ações</h2>

    <div class="row">
        <div class="col-md-3 d-flex align-items-stretch">
            <div class="card text-white bg-success mb-3 w-100">
                <div class="card-header fs-5"><i class="fa-solid fa-clipboard-check fa-lg me-2"></i>Vendas</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/conta-azul/sales" class="btn btn-outline-light btn-lg"><i class="fa-solid fa-calendar-check me-1"></i> Gerar vendas</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-stretch">
            <div class="card bg-light mb-3 w-100">
                <div class="card-header fs-5"><i class="fa-solid fa-right-to-bracket fa-lg me-2"></i> Acesso</div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        ⚠️ A última atualização do token de acesso foi <b>{{Carbon\Carbon::parse($tokenLastUpdate)->locale('pt_BR')->diffForHumans()}}</b>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="/conta-azul/refresh-token" class="btn btn-success btn-lg"><i class="fa-solid fa-lock-open me-1"></i> Renovar Acesso</a>
                        <a href="{{$authUrl}}" class="btn btn-outline-primary btn-lg"><i class="fa-solid fa-lock me-1"></i> Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <hr />

</div>

@endsection
