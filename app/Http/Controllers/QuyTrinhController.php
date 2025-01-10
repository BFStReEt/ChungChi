<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Policies\AdminPolicy;
use App\Services\QuyTrinhService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            $now = date('d-m-Y H:i:s');
            $stringTime = strtotime($now);
            DB::table('adminlogs')->insert([
                'admin_id' => Auth::guard('admin')->user()->id,
                'time' =>  $stringTime,
                'ip' => $request ? $request->ip() : null,
                'action' => 'export a file',
                'cat' => 'admin',
            ]);
            abort_if(!$this->permissionPolicy->hasPermission($this->user, 'QUY TRÌNH.export'), 403, "No permission");

            return $this->quyTrinhService->export($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
