@php
if(!isset($checked))
    $checked = old($name);
@endphp

<div class="form-group row">
    <div class="col-sm-10 offset-sm-2">
        <div class="form-check">
            <input type="radio" class="form-check-input{{ $errors->has($name) ? ' is-invalid' : '' }}" value="{{$value}}" name="{{$name}}" id="{{$name . $value}}" {{ $checked ? 'checked' : '' }}>
            <label for="{{$name . $value}}" class="form-check-label"> {{$label}}</label>
            @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>