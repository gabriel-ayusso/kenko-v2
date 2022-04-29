@php
    if(!isset($value))
        $value = old($name);

    if(!isset($placeholder))
        $placeholder = $label;
@endphp

<div class="form-group row">
    <label for="{{$name}}" class="col-sm-2 col-form-label">{{$label}}</label>
    <div class="col-sm-10">
        <textarea name="{{$name}}" id="{{$name}}" placeholder="{{$placeholder}}" class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}" cols="30" rows="10">{{$value}}</textarea>
        @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
        @endif
    </div>
</div>