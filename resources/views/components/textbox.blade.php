@php
   $step = '';
   if(!isset($value))
      $value = old($name);

   if(!isset($type))
      $type = 'text';
   else {
      if($type == 'number')
         $step = ' step=any';
   }

   if(!isset($placeholder))
      $placeholder = $label;
@endphp

<div class="form-group row">
  <label for="{{$name}}" class="col-sm-2 col-form-label">{{$label}}</label>
  <div class="col-md-10">
    <input type="{{$type}}" {{$step}} placeholder="{{$placeholder}}" class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}" name="{{$name}}" id="{{$name}}" value="{{$value}}" @if(isset($required)) required @endif> 
    @if(isset($help))
      <small id="{{$name}}HelpBlock" class="form-text text-muted">
         {{$help}}
      </small>
    @endif
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>