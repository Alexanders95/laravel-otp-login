<?php

namespace tpaksu\LaravelOTPLogin;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use tpaksu\LaravelOTPLogin\LoginMiddleware;
use tpaksu\LaravelOTPLogin\Models\OneTimePassword;

class OTPServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '../routes/routes.php');
        $this->loadViewsFrom(__DIR__ . '../resources/views', 'laravel-otp-login');
        $this->loadTranslationsFrom(__DIR__ . '../resources/translations', 'laravel-otp-login');

        $this->publishes([__DIR__ . '../config/config.php' => config_path('otp.php')], "config");
        $this->publishes([__DIR__ . '../resources/views' => resource_path('views/vendor/laravel-otp-login')], "views");
        $this->publishes([__DIR__ . '../database/migrations' => database_path('migrations')], "migrations");
        $this->publishes([__DIR__ . '/Services' => app_path('Services')], "services");
        $this->publishes([__DIR__ . '../resources/lang' => resource_path('lang/vendor/laravel-otp-login')]);

        $this->pushMiddleware();
        $this->hookLogoutEvent();
    }

    /**
     * Pushes the required middleware to intercept unverified requests and redirect to OTP verification page
     *
     * @return  void
     */
    private function pushMiddleware()
    {
        if (config("otp.otp_service_enabled", false) == true) {
            $this->app['router']->pushMiddlewareToGroup('web', LoginMiddleware::class);
        }
    }

    /**
     * Hooks to the user logout event and expires the OTP session cookie
     *
     * @return  void
     */
    private function hookLogoutEvent()
    {
        Event::listen('Illuminate\Auth\Events\Logout', function ($user) {
            if (config("otp.otp_service_enabled", false) == false) {
                return;
            }

            setcookie("otp_login_verified", "", time() - 3600);
            unset($_COOKIE['otp_login_verified']);

            OneTimePassword::where("user_id", $user->id)
                ->get()
                ->each(function ($otp) {
                    /** @var OneTimePassword $otp */
                    $otp->discardOldPasswords();
                });

            Session::forget("otp_service_bypass");
        });
    }
}
