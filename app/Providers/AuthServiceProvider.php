<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\EmployeeAvailability' => 'App\Policies\EmployeeAvailabilityPolicy',
        'App\Models\EmployeeUnavailability' => 'App\Policies\EmployeeUnavailabilityPolicy',
        'App\Models\Employee' => 'App\Policies\EmployeePolicy',
        'App\Models\Service' => 'App\Policies\ServicePolicy',
        'App\Models\Product' => 'App\Policies\ProductPolicy',
        'App\Models\AccountTransaction' => 'App\Policies\AccountTransactionPolicy',
        'App\Models\Booking' => 'App\Policies\BookingManagerPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Auth::provider('kenko', function ($app, array $config) {
            return new \App\Providers\KenkoUserProvider();
        });

        Gate::define('conta-azul', function (User $user){
            return $user->admin;
        });
    }
}
