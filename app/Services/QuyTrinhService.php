<?php

namespace App\Services;

use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Http\Request;
use App\Models\CateParent;
use Illuminate\Support\Facades\File as FileSystem;
use Illuminate\Support\Facades\Response;
use App\Models\File;

class QuyTrinhService
{
    protected $ImportFile;

    public function import(Request $request)
    {
        $file = $request->file('file');

        $request->validate([
            'file' => 'required|file|max:2024',
        ]);

        $fileName = $file->getClientOriginalName();

        $destinationPath = public_path('Quy trinh');

        $filePath = $destinationPath . '/' . $fileName;

        if (FileSystem::exists($filePath)) {
            return response()->json([
                'status' => false,
                'message' => 'File đã tồn tại.',
            ], 409);
        }

        if (!FileSystem::exists($destinationPath)) {
            FileSystem::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);
        $quyTrinhCategory = CateParent::where('name', 'Quy trinh')->first();
        $quyTrinhParentId = $quyTrinhCategory ? $quyTrinhCategory->id : null;

        $fileRecord = File::create([
            'name' => $fileName,
            'path' => 'Quy trinh/' . $fileName,
            'mime_type' => $file->getClientMimeType(),
            'parent_id' => $quyTrinhParentId,
            'child_id' => null,
            'year_id' => null,
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Import thành công.',
            'data' => $fileRecord,
        ]);
    }

    public function export(Request $request)
    {
        $fileName = $request->query('file');

        if (!$fileName) {
            throw new \InvalidArgumentException('Vui lòng cung cấp tên file.');
        }

        $filePath = public_path('Quy trinh/' . $fileName);

        if (!FileSystem::exists($filePath)) {
            throw new \RuntimeException('File không tồn tại.');
        }

        return response()->download($filePath, $fileName);
    }
}
