<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Form;
use App\Models\Page;
use App\Models\Post;
use App\Policies\EventPolicy;
use App\Policies\FormPolicy;
use App\Policies\PagePolicy;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Page::class  => PagePolicy::class,
        Post::class  => PostPolicy::class,
        Event::class => EventPolicy::class,
        Form::class  => FormPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}