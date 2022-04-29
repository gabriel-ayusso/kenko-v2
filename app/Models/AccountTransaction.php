<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AccountTransaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = ['date', 'employee_id', 'service_id', 'booking_id', 'amount', 'description', 'account_cycle_id'];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function cycle()
    {
        return $this->belongsTo('App\Models\AccountCycle', 'account_cycle_id');
    }

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
