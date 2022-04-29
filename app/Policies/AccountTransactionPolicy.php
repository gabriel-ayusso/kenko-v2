<?php

namespace App\Policies;

use App\Models\AccountTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountTransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any account transactions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->admin || $user->manager;
    }

    /**
     * Determine whether the user can view the account transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountTransaction  $accountTransaction
     * @return mixed
     */
    public function view(User $user, AccountTransaction $accountTransaction)
    {
        return $user->admin || $user->manager;
    }

    /**
     * Determine whether the user can create account transactions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->admin || $user->manager;
    }

    /**
     * Determine whether the user can update the account transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountTransaction  $accountTransaction
     * @return mixed
     */
    public function update(User $user, AccountTransaction $accountTransaction)
    {
        return $user->admin || $user->manager;
    }

    /**
     * Determine whether the user can delete the account transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountTransaction  $accountTransaction
     * @return mixed
     */
    public function delete(User $user, AccountTransaction $accountTransaction)
    {
        return $user->admin || $user->manager;
    }

    /**
     * Determine whether the user can restore the account transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountTransaction  $accountTransaction
     * @return mixed
     */
    public function restore(User $user, AccountTransaction $accountTransaction)
    {
        return $user->admin || $user->manager;
    }

    /**
     * Determine whether the user can permanently delete the account transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccountTransaction  $accountTransaction
     * @return mixed
     */
    public function forceDelete(User $user, AccountTransaction $accountTransaction)
    {
        return $user->admin || $user->manager;
    }
}
