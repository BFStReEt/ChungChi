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
}
