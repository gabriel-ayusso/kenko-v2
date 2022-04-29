<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EmployeeAvailability extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['employee_id', 'weekday', 'start', 'end'];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];


    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
