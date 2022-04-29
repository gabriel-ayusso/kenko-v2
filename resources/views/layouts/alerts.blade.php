@if (session('status'))
<div class="alert alert-success" role="alert">
    {{ session('status') }}
</div>
@endif

@foreach (['danger', 'warning', 'success', 'info'] as $key)
@if(session($key))
<div class="alert alert-{{$key}}">
    {{ session($key) }}
</div>
@endif
@endforeach