<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

class Employee extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'employees';
    protected $fillable = ['firstname', 'lastname', 'title', 'description'];

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'employee_service')->withPivot('comission');
    }

    public function availabilities()
    {
        return $this->hasMany('App\Models\EmployeeAvailability');
    }

    public function unavailabilities()
    {
        return $this->hasMany('App\Models\EmployeeUnavailability');
    }

    public function bookings()
    {
        return $this->hasMany('App\Models\Booking');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\AccountTransaction');
    }

    public function hasAvailabilityOn(Carbon $start, Carbon $end)
    {
        $myStart = Carbon::create(1970, 1, 1, $start->hour, $start->minute, $start->second);
        $myEnd = Carbon::create(1970, 1, 1, $end->hour, $end->minute, $end->second);
        $weekday = $start->dayOfWeek;
        $query = sprintf(
            "select count(1) as count from employee_availabilities where employee_id = %d and weekday = %d and '%s' between start and end and '%s' between start and end",
            $this->id,
            $weekday,
            $myStart,
            $myEnd
        );
        //Log::debug('hasAvailabilityOn: ' . $query);
        return DB::select($query)[0]->count > 0;
    }

    public function hasUnavailabilityOn(Carbon $start, Carbon $end)
    {
        $query = sprintf(
            "select count(1) as count from employee_unavailabilities where employee_id = %d and (( '%s' > start and '%s' < end) or ('%s' > start and '%s' < end))",
            $this->id,
            $start,
            $start,
            $end,
            $end
        );
        //Log::debug('hasUnavailabilityOn: ' .  $query);
        return DB::select($query)[0]->count > 0;
    }

    public function hasBookingOn(Carbon $start, Carbon $end)
    {
        $query = sprintf(
            "select count(1) as count from bookings
            where
                status <> 'C'
                and employee_id = %d
                and (
                    ('%s' >= date and '%s' < date_add(date, interval duration minute)) or
                    ('%s' > date and '%s' <= date_add(date, interval duration minute)) or
                    (date >= '%s' and date <= '%s') or
                    (date_add(date, interval duration minute) >= '%s' and date_add(date, interval duration minute) <= '%s') or
                    (date >= '%s' and date < '%s')
                );",
            $this->id,
            $start,
            $start,
            $end,
            $end,
            $start,
            $start,
            $end,
            $end,
            $start,
            $end
        );
        //Log::debug('hasBookingOn: ' .  $query);
        return DB::select($query)[0]->count > 0;
    }

    public function isAvailableOn(Carbon $start, $duration)
    {
        $end = $start->clone()->addMinutes($duration);

        $maxDays = intval(env('APP_BOOKING_MAX_DAYS'));
        $limit = Carbon::parse('today')->addDays($maxDays);
        if ($start > $limit) {
            return false;
        }

        // se está fora do período...
        if (!$this->hasAvailabilityOn($start, $end)) {
            return false;
        }

        // se está indisponível nesse horário...
        if ($this->hasUnavailabilityOn($start, $end)) {
            return false;
        }

        // se já tem algum agendamento...
        if ($this->hasBookingOn($start, $end)) {
            return false;
        }

        // do contrário
        return true;
    }

    public function availabeTimes(Carbon $date, $duration)
    {
        $now = Carbon::now(); // para validar se o horário é passado
        $step = intval(env('APP_BOOKING_INTERVAL'));
        $totalWindows = 1440 / $step;
        $availabilities = [];

        $currentTime = $date->clone();

        for ($i = 0; $i < $totalWindows; $i++) {
            if ($now > $currentTime) {
                $currentTime->addMinutes($step);
                continue;
            }

            if ($this->isAvailableOn($currentTime, $duration)) {
                $availabilities[] = (object) [
                    'time' => $currentTime->format('Y-m-d H:i:s'),
                    'employee' => $this
                ];
            }

            $currentTime->addMinutes($step);
        }

        return $availabilities;
    }
}
