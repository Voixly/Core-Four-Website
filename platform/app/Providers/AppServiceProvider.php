<?php

namespace App\Providers;

use App\Models\City;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::defaultView('pagination.simple');
        Paginator::defaultSimpleView('pagination.simple');
        Blade::directive('assetv', function (string $expression) {
            return "<?php echo e(\\App\\Support\\Assets::url($expression)); ?>";
        });

        View::share('officePhone', config('app.office_phone'));
        View::share('officePhoneTel', config('app.office_phone_tel'));
        View::share('officeAddress', config('app.office_address'));
        View::share('officeHours', 'Mon–Sat 7am–7pm · Emergency 24/7');

        View::composer('layouts.public', function ($view) {
            try {
                if (Schema::hasTable('cities')) {
                    $view->with('footerCities', City::query()
                        ->where('type', 'residential')
                        ->where('metro', 'Houston')
                        ->where('slug', '!=', 'tx')
                        ->orderBy('name')
                        ->get());
                }
            } catch (\Throwable) {
                $view->with('footerCities', collect());
            }
        });

        try {
            if (Schema::hasTable('users') && Schema::hasTable('settings') && Setting::get('bootstrap_admin_password_v2') !== '1') {
                (new \Database\Seeders\EnsureAdminSeeder)->run();
            }
        } catch (\Throwable) {
            // The staff login can still be repaired on the next boot.
        }

        try {
            if (Schema::hasTable('settings')) {
                View::composer('*', function ($view) {
                    $settings = Setting::query()->pluck('value', 'key');
                    $view->with('siteSettings', $settings);
                    if ($settings->get('office_phone')) {
                        $view->with('officePhone', $settings->get('office_phone'));
                    }
                    if ($settings->get('office_address')) {
                        $view->with('officeAddress', $settings->get('office_address'));
                    }
                    if ($settings->get('hours')) {
                        $view->with('officeHours', $settings->get('hours'));
                    }
                });
            }
        } catch (\Throwable) {
            // Database may not be ready during first install.
        }
    }
}
