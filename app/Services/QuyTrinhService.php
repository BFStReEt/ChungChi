<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\CateParent;
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
        $destinationPath = public_path('files/file');
        $filePath = $destinationPath . '/' . $fileName;

        $quyTrinhCategory = CateParent::where('name', 'Quy trình')->first();
        $quyTrinhParentId = $quyTrinhCategory ? $quyTrinhCategory->id : null;
        abort_if(File::exists($filePath), 409, "File đã tồn tại");

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $fileName);
        $fileRecord = File::create([
            'name' => $fileName,
            'path' => $filePath,
            'mime_type' => $file->getClientMimeType(),
            'parent_id' => $quyTrinhParentId,
            'child_id' => null,
            'year_id' => null,
            'description' => $request->description ?? null,
        ]);

        $this->ImportFile = storage_path('app/' . $filePath);

        return [
            'file_name' => $fileName,
            'file_path' => 'files/file/' . $fileName,
            'file_record' => $fileRecord
        ];
    }

    public function export(Request $request) {}
}
