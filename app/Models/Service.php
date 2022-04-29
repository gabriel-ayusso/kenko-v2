<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Service extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name', 'description', 'time', 'price', 'comission', 'private', 'category_id', 'ca_id'];

    public function employees()
    {
        return $this->belongsToMany('App\Models\Employee', 'employee_service')->withPivot('comission');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ServiceCategory', 'category_id');
    }
}
