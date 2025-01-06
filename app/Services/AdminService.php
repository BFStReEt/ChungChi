<?php

namespace App\Services;

use App\Services\Interfaces\AdminServiceInterface;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class AdminService implements AdminServiceInterface
{
    public function store($request)
    {
        if (Gate::allows('THÔNG TIN QUẢN TRỊ.Quản lý tài khoản admin.add')) {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Vui lòng nhập tên và mật khẩu',
                    'errors' => $validator->errors()
                ], 422);
            }
            $check = Admin::where('username', $$request->username)->first();
            if ($check != '') {
                return response()->json([
                    'message' => 'Tên đăng nhập bị trùng, vui lòng nhập lại',
                    'status' => false
                ], 202);
            }
        }
    }
}
