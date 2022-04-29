<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EmployeeUnavailability extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['employee_id', 'start', 'end', 'description'];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];
}
