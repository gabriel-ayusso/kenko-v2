<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

class AccountCycle extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = ['start', 'end'];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    public static function current()
    {
        $cycle = AccountCycle::whereRaw("(current_date() >= start and current_date() <= end) or (current_date() >= start and end is null)")->orderBy('start', 'desc')->first();
        return $cycle;

        // return AccountCycle::where(function ($query) {
        //     $query->where('end', '>=', strtotime('today'))->orWhereNull('end');
        // })->orderBy('start', 'desc')->first();
    }
}
