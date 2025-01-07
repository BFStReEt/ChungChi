<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        $admins = Admin::all();

        $adminPermissions = $admins->map(function ($admin) use ($permissions) {
            $roles = $admin->roles;
            $permissionsOfAdmin = [];

            foreach ($roles as $role) {
                foreach ($role->permissions as $permission) {
                    $permissionsOfAdmin[] = $permission->slug;
                }
            }

            return [
                'username' => $admin->username,
                'admin_id' => $admin->id,
                'admin_name' => $admin->display_name,
                'permissions' => array_unique($permissionsOfAdmin),
            ];
        });

        return response()->json([
            'status' => true,
            'admin_permissions' => $adminPermissions,
        ]);
    }
    public function hehe()
    {
        if (Gate::allows('add')) {
            return response("hehe", 200);
        } else {
            return response()->json([
                'status' => false,
                'mess' => 'no permission',
            ]);
        }
    }
}
