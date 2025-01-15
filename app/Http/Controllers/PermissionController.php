<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Policies\AdminPolicy;

class PermissionController extends Controller
{
    protected function hasPermission(Admin $admin, $slug)
    {
        $normalizedSlug = Str::slug($slug);

        $permissions = $admin->roles->flatMap(function ($role) {
            return $role->permissions->pluck('slug');
        });

        $normalizedPermissions = $permissions->map(function ($permission) {
            return Str::slug($permission);
        });

        return $normalizedPermissions->contains($normalizedSlug);
    }
}
