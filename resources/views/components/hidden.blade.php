@php
  if(!isset($value))
    $value = old($name);
@endphp
<input type="hidden" name="{{$name}}" id="{{$name}}" value="{{$value}}"> 