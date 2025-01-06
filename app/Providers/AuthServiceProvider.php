<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Permission;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    // protected $policies = [
    //     'App\Models\Model' => 'App\Policies\ModelPolicy',
    // ];
    public function boot(): void
    {
        $this->registerPolicies();

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            Gate::define($permission->slug, function ($user = null) use ($permission) {
                $user = Auth::guard('admin')->user();
                if (!$user || !($user instanceof \App\Models\Admin)) {
                    return false;
                }
                return $user->hasPermission($permission->slug);
            });
        }
    }
}
