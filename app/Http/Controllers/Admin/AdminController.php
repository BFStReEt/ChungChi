<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Services\Interfaces\AdminServiceInterface as AdminService;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function store(Request $request)
    {
        try {
            // $now = now()->timestamp;
            // DB::table('adminlogs')->insert([
            //     'admin_id' => Auth::guard('admin')->user()->id,
            //     'time' => $now,
            //     'ip' => $request->ip() ?? null,
            //     'action' => 'add a admin',
            //     'cat' => 'admin',
            // ]);
            $store = $this->adminService->store($request);
            return $store;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function login(Request $request)
    {
        try {
            $login = $this->adminService->login($request);
            return $login;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
