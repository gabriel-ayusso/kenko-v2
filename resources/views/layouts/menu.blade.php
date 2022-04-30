@guest
<li class="nav-item">
    <a class="nav-link" href="https://kenkostudio.com.br"><i class="fas fa-home fa-lg"></i> Home</a>
</li>
@endguest
@auth

@if(Auth::user()->employee_id > 0)
<li class="nav-item {{ Route::is('home') || Route::is('dashboard') ? 'active' : '' }}">
    <a class="nav-link" href="{{ url('/') }}"><i class="fas fa-user-alt fa-lg"></i> Minha Página</a>
</li>
@endif
@if(Auth::user()->agenda)
<li class="nav-item {{ Route::is('home') || Route::is('dashboard') ? 'active' : '' }}">
    <a class="nav-link" href="{{route('manager.weekly')}}"><i class="far fa-calendar-check fa-lg me-2"></i> Semana</a>
</li>
@endif
@if(Auth::user()->admin)
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" data-bs-toggle="dropdown">
        <i class="fas fa-cogs fa-lg"></i> Admin
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">
        <a class="dropdown-item" href="{{ route('employees.index') }}"><i class="far fa-id-badge"></i> Funcionários</a>
        <a class="dropdown-item" href="{{ route('services.index') }}"><i class="fas fa-spa"></i> Serviços</a>
        <a class="dropdown-item" href="{{ route('products.index') }}"><i class="fas fa-box"></i> Produtos</a>
        <a class="dropdown-item" href="{{ route('categories.index') }}"><i class="far fa-bookmark"></i> Categorias</a>
        <a class="dropdown-item" href="{{ route('users.index') }}"><i class="fas fa-user-friends"></i> Usuários</a>
        <a class="dropdown-item" href="{{ route('cycles.index') }}"><i class="far fa-calendar-minus"></i> Ciclos de Faturamento</a>
        <a class="dropdown-item" href="{{ route('transactions.index') }}"><i class="fas fa-money-check-alt"></i> Transações</a>
        <a class="dropdown-item" href="{{ route('configuration.index') }}"><i class="fas fa-cogs"></i> Configurações</a>
        <a class="dropdown-item" href="{{ route('conta-azul.index') }}"><i class="fas fa-file-invoice-dollar"></i> Conta Azul</a>
    </div>
</li>
@endif

@if(Auth::user()->manager)
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownManager" data-bs-toggle="dropdown">
        <i class="fas fa-id-card-alt fa-lg"></i> Gerente
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdownManager">
        <a class="dropdown-item" href="{{ url('/manager/dashboard') }}"><i class="fas fa-tasks fa-lg"></i> Visão Geral</a>
        <a class="dropdown-item" href="{{route('manager.weekly2')}}"><i class="far fa-calendar-check fa-lg me-2"></i> Semana</a>
        <a class="dropdown-item" href="{{route('manager.nextday')}}"><i class="fas fa-calendar-day fa-lg me-2"></i> Visão Dia</a>
        <a class="dropdown-item" href="{{route('manager.customer')}}"><i class="fas fa-person-rays fa-lg me-2"></i> Cliente</a>
        <a class="dropdown-item" href="{{ route('transactions.index') }}"><i class="fas fa-money-check-alt"></i> Transações</a>
        <a class="dropdown-item" href="{{ route('booking.index') }}"><i class="far fa-calendar-alt"></i> Agendamentos</a>
    </div>
</li>
@endif

@endauth

<li class="nav-item {{ Route::is('bookings*') ? 'active' : '' }}">
    <a class="nav-link " href="{{ route('bookings.index') }}"><i class="far fa-calendar-alt fa-lg"></i> Agendamento</a>
</li>
