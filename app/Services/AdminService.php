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
            $data = $request->only([
                'username',
                'password',
                'email',
                'display_name',
                'avatar',
                'phone',
                'status',
                'depart_id',
            ]);
            $userAdmin = new Admin();
            $userAdmin->username = $request['username'];
            $userAdmin->password = Hash::make($request['password']);
            $userAdmin->email = $request['email'];
            $userAdmin->display_name = $request['display_name'];

            $filePath = '';
            $disPath = public_path();

            if ($request->avatar != null) {

                $DIR = $disPath . '\uploads\admin';
                $httpPost = file_get_contents('php://input');

                $file_chunks = explode(';base64,', $request->avatar[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];
                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'admin/' . $name . '.png';
                file_put_contents($file,  $base64Img);
            }
            $userAdmin->avatar = $filePath;

            $userAdmin->skin = "";
            $userAdmin->is_default = 0;
            $userAdmin->lastlogin = 0;
            $userAdmin->code_reset = Hash::make($request['password']);
            $userAdmin->menu_order = 0;
            $userAdmin->phone = $request['phone'];
            $userAdmin->status = $request['status'];
            $userAdmin->depart_id = $request['depart_id'];

            $userAdmin->save();
            $userAdmin->roles()->attach($request->input('role_id'));
            return response()->json([
                'status' => true,
                'userAdmin' => $userAdmin,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'mess' => 'no permission',
            ]);
        }
    }
}
