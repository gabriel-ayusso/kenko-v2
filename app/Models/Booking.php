<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

class Booking extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'cpf',
        'name',
        'email',
        'phone',
        'service_id',
        'employee_id',
        'date',
        'status',
        'duration',
        'confirmation_hash',
        'reminder_date',
        'ip',
        'user_id',
        'ca_int_date',
        'ca_sale_id',
        'ca_client_id',
        'ca_service_id',
        'comments',
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function service()
    {
        return $this->belongsTo('\App\Models\Service');
    }

    public function employee()
    {
        return $this->belongsTo('\App\Models\Employee');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\AccountTransaction', 'booking_id');
    }

    public function caCustomer()
    {
        return $this->hasOne('App\Models\CaCustomer', 'id', 'ca_customer_id');
    }

    public function caService()
    {
        return $this->hasOne('App\Models\CaService', 'id', 'ca_service_id');
    }

    public function addComission()
    {
        $service = $this->service;

        $specialComission = DB::table('employee_service')
            ->where([
                'employee_id' => $this->employee_id,
                'service_id' => $this->service_id
            ])->select('comission')->first();

        $amount = 0;
        $description = "";
        if ($specialComission && $specialComission->comission > 0) {
            $amount = $service->price * ($specialComission->comission / 100);
            $description = "{$specialComission->comission}% (cond. esp.) sobre {$service->price} pelo serviço {$service->name} prestado para {$this->name}.";
        } else {
            $amount = $service->price * ($service->comission / 100);
            $description = "{$service->comission}% sobre {$service->price} pelo serviço {$service->name} prestado para {$this->name}.";
        }

        $cycles = DB::select("select id from account_cycles where start <= date(:date1) and (end is null or end >= date(:date2)) order by id desc limit 1;", ['date1' => $this->date, 'date2' => $this->date]);
        $cycle_id = count($cycles) > 0 ? $cycles[0]->id : null;

        AccountTransaction::create([
            'date' => $this->date,
            'employee_id' => $this->employee_id,
            'service_id' => $this->service_id,
            'booking_id' => $this->id,
            'amount' => $amount,
            'description' => $description,
            'account_cycle_id' => $cycle_id
        ]);
    }

    public function recalculateComission()
    {
        DB::update("delete from account_transactions where booking_id = ?", [$this->id]);
        $this->addComission();
    }
}
