@php
if(!isset($checked))
    $checked = old($name);

if (!isset($value))
    $value = true;
@endphp

<div class="form-group row">
    <div class="col-sm-10 offset-sm-2">
        <div class="form-check">
            <input type="checkbox" class="form-check-input{{ $errors->has($name) ? ' is-invalid' : '' }}" value="{{$value}}" name="{{$name}}" id="{{$name . $value}}" {{ $checked ? 'checked' : '' }}>
            <label for="{{$name . $value}}" class="form-check-label"> {{$label}}</label>
            @if(isset($help))
            <small id="{{$name}}HelpBlock" class="form-text text-muted">{{$help}}</small>
            @endif
            @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>