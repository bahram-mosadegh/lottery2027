<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Applicant;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Gate::define('view_steps', function (User $user = null, $applicant_id = null) {
            if (!$user && (!session('applicant_id') || session('applicant_id') != $applicant_id)) {
                return false;
            }

            if ($user && $user->role == 'agent' && $applicant_id) {
                $applicant = Applicant::find($applicant_id);

                if ($applicant->user_id != $user->id) {
                    return false;
                }
            }

            return true;
        });
    }
}
