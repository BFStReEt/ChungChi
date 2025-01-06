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
        //     if (Gate::allows('THÔNG TIN QUẢN TRỊ.Quản lý tài khoản admin.add')) {
        // Xác thực đầu vào
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'email' => 'nullable|email',
            'display_name' => 'nullable|string',
            'avatar' => 'nullable|array',
            'phone' => 'nullable|string',
            'status' => 'nullable|integer',
            'depart_id' => 'nullable|integer',
            'role_id' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra tên đăng nhập
        $check = Admin::where('username', $request->username)->first();
        if ($check) {
            return response()->json([
                'message' => 'Tên đăng nhập đã tồn tại, vui lòng thử tên khác',
                'status' => false
            ], 409);
        }

        // Chuẩn bị dữ liệu
        $userAdmin = new Admin();
        $userAdmin->username = $request->username;
        $userAdmin->password = Hash::make($request->password);
        $userAdmin->email = $request->email;
        $userAdmin->display_name = $request->display_name;

        // Xử lý ảnh đại diện (avatar)
        $filePath = '';
        if (!empty($request->avatar) && is_array($request->avatar)) {
            $avatarData = $request->avatar[0] ?? null;

            if ($avatarData && str_contains($avatarData, ';base64,')) {
                $file_chunks = explode(';base64,', $avatarData);
                $fileType = explode('image/', $file_chunks[0] ?? '');

                if (isset($file_chunks[1], $fileType[1])) {
                    $base64Img = base64_decode($file_chunks[1]);
                    $imageType = $fileType[1];

                    $uploadDir = public_path('uploads' . DIRECTORY_SEPARATOR . 'admin');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = uniqid() . '.' . $imageType;
                    $filePath = 'uploads/admin/' . $fileName;
                    file_put_contents($uploadDir . DIRECTORY_SEPARATOR . $fileName, $base64Img);
                }
            }
        }
        $userAdmin->avatar = $filePath;

        // Gán các thuộc tính khác
        $userAdmin->skin = '';
        $userAdmin->is_default = 0;
        $userAdmin->lastlogin = NULL;
        $userAdmin->code_reset = Hash::make($request->password);
        $userAdmin->menu_order = 0;
        $userAdmin->phone = $request->phone;
        $userAdmin->status = $request->status;
        $userAdmin->depart_id = $request->depart_id;

        // Lưu bản ghi admin
        $userAdmin->save();

        // Gán vai trò cho admin
        if ($request->has('role_id') && is_array($request->role_id)) {
            $userAdmin->roles()->attach($request->role_id);
        }

        return response()->json([
            'status' => true,
            'userAdmin' => $userAdmin,
        ]);
        // } else {
        //     return response()->json([
        //         'status' => false,
        //         'mess' => 'no permission',
        //     ]);
        // }
    }
}
