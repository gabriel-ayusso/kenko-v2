<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingManagerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any bookings.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->manager || $user->agenda;
    }

    /**
     * Determine whether the user can view the booking.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function view(User $user, Booking $booking)
    {
        return $user->manager || $user->agenda;
    }

    /**
     * Determine whether the user can create bookings.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->manager || $user->agenda;
    }

    /**
     * Determine whether the user can update the booking.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function update(User $user, Booking $booking)
    {
        return $user->manager || $user->agenda;
    }

    /**
     * Determine whether the user can delete the booking.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function delete(User $user, Booking $booking)
    {
        return $user->manager;
    }

    /**
     * Determine whether the user can restore the booking.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function restore(User $user, Booking $booking)
    {
        return $user->manager;
    }

    /**
     * Determine whether the user can permanently delete the booking.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function forceDelete(User $user, Booking $booking)
    {
        return $user->manager;
    }
}
