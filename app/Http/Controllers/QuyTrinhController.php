<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Policies\AdminPolicy;
use App\Services\QuyTrinhService;

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
        abort_if(!$this->permissionPolicy->hasPermission($this->user, 'HỒ SƠ.import'), 403, "No permission");
        $result = $this->quyTrinhService->import($request);
        return response()->json([
            'status' => true,
            'message' => 'Import thành công',
            'data' => $result,
        ]);
    }
}
