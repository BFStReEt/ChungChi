<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Policies\AdminPolicy;

class PermissionController extends Controller
{
    protected $user;
    protected $permissionPolicy;

    public function __construct(AdminPolicy $permissionPolicy)
    {
        $this->user = auth('admin')->user();

        $this->permissionPolicy = $permissionPolicy;
    }

    public function hehe()
    {
        if ($this->permissionPolicy->hasPermission($this->user, 'add')) {
            return response()->json([
                'status' => true,
                'message' => 'hehe',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'User does not have permission to add.',
        ]);
    }
}
