<?php

namespace App\Policies;

use App\Models\EmployeeUnavailability;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class EmployeeUnavailabilityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any employee unavailabilities.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->admin || $user->employee_id > 0;
    }

    /**
     * Determine whether the user can view the employee unavailability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeUnavailability  $unavailability
     * @return mixed
     */
    public function view(User $user, EmployeeUnavailability $unavailability)
    {
        return $user->admin || $user->employee_id > 0;
    }

    /**
     * Determine whether the user can create employee unavailabilities.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->admin || $user->employee_id > 0;
    }

    /**
     * Determine whether the user can update the employee unavailability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeUnavailability  $unavailability
     * @return mixed
     */
    public function update(User $user, EmployeeUnavailability $unavailability)
    {
        return $user->admin || $user->employee_id == $unavailability->employee_id;
    }

    /**
     * Determine whether the user can delete the employee unavailability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeUnavailability  $unavailability
     * @return mixed
     */
    public function delete(User $user, EmployeeUnavailability $unavailability)
    {
        return $user->admin || $user->employee_id == $unavailability->employee_id;
    }

    /**
     * Determine whether the user can restore the employee unavailability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeUnavailability  $unavailability
     * @return mixed
     */
    public function restore(User $user, EmployeeUnavailability $unavailability)
    {
        return $user->admin || $user->employee_id == $unavailability->employee_id;
    }

    /**
     * Determine whether the user can permanently delete the employee unavailability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EmployeeUnavailability  $unavailability
     * @return mixed
     */
    public function forceDelete(User $user, EmployeeUnavailability $unavailability)
    {
        return $user->admin || $user->employee_id == $unavailability->employee_id;
    }
}
