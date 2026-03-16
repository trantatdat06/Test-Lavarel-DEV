<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Thêm dòng này
use App\Models\Tag;                  // Thêm dòng này

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
        // Tự động chia sẻ danh sách Tag cho tất cả các màn hình giao diện
        if (\Illuminate\Support\Facades\Schema::hasTable('tags')) {
            View::share('availableTags', Tag::all());
        }
    }
}