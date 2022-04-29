<?php

namespace App\Policies;

use App\Models\EmployeeAvailability;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class EmployeeAvailabilityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any employee availabilities.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->admin || $user->employee_id > 0;
    }

    /**
     * Determine whether the user can view the employee availability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeAvailability  $availability
     * @return mixed
     */
    public function view(User $user, EmployeeAvailability $availability)
    {
        return $user->admin || $user->employee_id == $availability->employee_id;
    }

    /**
     * Determine whether the user can create employee availabilities.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->admin || $user->employee_id > 0;
    }

    /**
     * Determine whether the user can update the employee availability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeAvailability  $availability
     * @return mixed
     */
    public function update(User $user, EmployeeAvailability $availability)
    {
        return $user->admin || $user->employee_id == $availability->employee_id;
    }

    /**
     * Determine whether the user can delete the employee availability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeAvailability  $availability
     * @return mixed
     */
    public function delete(User $user, EmployeeAvailability $availability)
    {
        return $user->admin || $user->employee_id == $availability->employee_id;
    }

    /**
     * Determine whether the user can restore the employee availability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeAvailability  $availability
     * @return mixed
     */
    public function restore(User $user, EmployeeAvailability $availability)
    {
        return $user->admin || $user->employee_id == $availability->employee_id;
    }

    /**
     * Determine whether the user can permanently delete the employee availability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeAvailability  $availability
     * @return mixed
     */
    public function forceDelete(User $user, EmployeeAvailability $availability)
    {
        return $user->admin || $user->employee_id == $availability->employee_id;
    }
}
