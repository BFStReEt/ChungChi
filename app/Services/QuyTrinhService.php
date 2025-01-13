<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\CateParent;
use Illuminate\Support\Facades\File as FileSystem;
use Illuminate\Support\Facades\Response;
use App\Models\File;
use Illuminate\Support\Str;

class QuyTrinhService
{
    protected $ImportFile;

    public function import(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:2024',
        ]);

        $files = $request->file('files');
        $destinationPath = public_path('Quy trinh');

        $quyTrinhCategory = CateParent::where('name', 'Quy trinh')->first();
        $quyTrinhParentId = $quyTrinhCategory ? $quyTrinhCategory->id : null;

        $importedFiles = [];
        $existingFiles = [];

        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $filePath = $destinationPath . '/' . $fileName;

            if (FileSystem::exists($filePath)) {
                $existingFiles[] = $fileName;
                continue;
            }

            if (!FileSystem::exists($destinationPath)) {
                FileSystem::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);

            $fileRecord = File::create([
                'name' => $fileName,
                'path' => 'Quy trinh/' . $fileName,
                'mime_type' => $file->getClientMimeType(),
                'parent_id' => $quyTrinhParentId,
                'child_id' => null,
                'year_id' => null,
                'description' => $request->input('description'),
            ]);

            $importedFiles[] = $fileRecord;
        }

        // Trả về kết quả
        return response()->json([
            'status' => true,
            'message' => 'Import thành công.',
            'imported_files' => $importedFiles,
            'existing_files' => $existingFiles,
        ]);
    }
}
