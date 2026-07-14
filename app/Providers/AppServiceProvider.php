<?php

namespace App\Providers;

use App\Services\CategoryService;
use App\Services\WishlistService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The header (and its dropdown/mobile menus) needs category data on
        // every front page regardless of which controller rendered it — a
        // view composer avoids repeating "fetch categories for the nav" in
        // every controller. Composed on the header wrapper itself (not on
        // `nav` directly) since BOTH the desktop nav and the mobile menu
        // need the same data — composing the parent once and passing it down
        // as a prop to both children avoids running the query twice.
        View::composer('components.front.layouts.header', function ($view) {
            $view->with('categories', app(CategoryService::class)->navigation());

            $userId = Auth::guard('web')->id();
            $view->with('wishlistCount', $userId ? app(WishlistService::class)->countForUser($userId) : 0);
        });

        // Laravel's built-in ResetPassword notification hardcodes a lookup
        // for a route literally named "password.reset" when building the
        // emailed reset link. This project's front routes are all prefixed
        // "front." (front.password.reset) per the area-based naming
        // convention, so the default lookup would throw a
        // RouteNotFoundException — this override points it at the real name.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return route('front.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    }
}
