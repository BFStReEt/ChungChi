<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use App\Policies\AdminPolicy;
use App\Services\QuyTrinhService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuyTrinhController extends Controller
{
    protected $permissionPolicy, $quyTrinhService;
    protected $user;

    public function __construct(AdminPolicy $permissionPolicy, QuyTrinhService $quyTrinhService)
    {
        $this->user = auth('admin')->user();

        $this->quyTrinhService = $quyTrinhService;
        $this->permissionPolicy = $permissionPolicy;
    }
    public function import(Request $request)
    {
        try {
            $now = date('d-m-Y H:i:s');
            $stringTime = strtotime($now);
            DB::table('adminlogs')->insert([
                'admin_id' => Auth::guard('admin')->user()->id,
                'time' =>  $stringTime,
                'ip' => $request ? $request->ip() : null,
                'action' => 'import a file',
                'cat' => 'admin',
            ]);
            abort_if(!$this->permissionPolicy->hasPermission($this->user, 'QUY TRÌNH.import'), 403, "No permission");
            $result = $this->quyTrinhService->import($request);
            return response()->json([
                'status' => true,
                'message' => 'Import thành công',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function export(Request $request)
    {
        try {
            abort_if(!$this->permissionPolicy->hasPermission($this->user, 'QUY TRÌNH.export'), 403, "No permission");

            $filePath = public_path('Quy trinh/' . $request->url);
            $fileName = Str::afterLast($request->url, '/');

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File không tồn tại.'], 404);
            }

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => 'false',
                'error' => $errorMessage
            ];
            return response()->json($response, 500);
        }
    }

    public function delete(string $id)
    {
        try {
            abort_if(!$this->permissionPolicy->hasPermission($this->user, 'QUY TRÌNH.delete'), 403, "No permission");
            $file = File::where('id', $id)->first();
            if (!$file) {
                return response()->json([
                    'status' => false,
                    'message' => 'File không tồn tại.'
                ], 404);
            }
            $filePath = public_path($file->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'File không tồn tại trong thư mục.'
                ], 404);
            }

            $file->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa file thành công.'
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => false,
                'error' => $errorMessage
            ];
            return response()->json($response, 500);
        }
    }
}
