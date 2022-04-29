<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KenkoUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        return User::where(['id' => $identifier, 'remember_token' => $token])->first();
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        User::where('id', $user->id)->update(['remember_token' => $token]);
    }

    public function retrieveByCredentials(array $credentials)
    {
        return User::where('email', $credentials['email'])->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (
            $user->email === $credentials['email']
            && $user->active == true
            && Hash::check($credentials['password'], $user->getAuthPassword())
        ) {
            $user->last_login = Carbon::now();
            $user->save();
            return true;
        }

        return false;
    }
}
