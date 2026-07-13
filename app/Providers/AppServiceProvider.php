<?php

namespace App\Providers;

use App\Services\CategoryService;
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
        });
    }
}
