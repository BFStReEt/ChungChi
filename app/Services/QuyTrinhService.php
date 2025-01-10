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
        $filePath = $file->storeAs('imports', $fileName);

        $quyTrinhCategory = CateParent::where('name', 'Quy Trình')->first();
        $quyTrinhParentId = $quyTrinhCategory ? $quyTrinhCategory->id : null;

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
            'filePath' => $filePath,
            'file_record' => $fileRecord
        ];
    }

    public function export(Request $request) {}
}
