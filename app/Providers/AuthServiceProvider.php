<?php

namespace App\Providers;

use App\Models\Post;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // In-app gates
        Gate::define('manage-categories', fn (User $user) => $user->is_admin);
        Gate::define('edit-post', fn (User $user, Post $post) => $user->id === $post->user_id);
        Gate::define('delete-post', fn (User $user, Post $post) => $user->id === $post->user_id || $user->is_admin);
        
        // Api gates
        Gate::define('api-manage-categories', fn (User $user) => $user->tokenCan('category:manage') && $user->is_admin);
        Gate::define('api-create-post', fn (User $user) => $user->tokenCan('post:store'));
        Gate::define('api-edit-post', fn (User $user, Post $post) => $user->tokenCan('post:update') && $user->id === $post->user_id);
        Gate::define('api-delete-post', fn (User $user, Post $post) => $user->tokenCan('post:destroy') && ($user->id === $post->user_id || $user->is_admin));
    }
}
