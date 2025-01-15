<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FileSystem;

class FilesController extends Controller
{
    public function import(Request $request, $categorySlug, $subCategorySlug = null, $yearSlug = null)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file'
        ]);

        $files = $request->file('files');

        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $subCategory = $subCategorySlug ? Category::where('slug', $subCategorySlug)->where('parent_id', $category->id)->first() : null;
        $yearCategory = $yearSlug ? Category::where('slug', $yearSlug)->where('parent_id', $subCategory->id ?? null)->first() : null;

        $destinationPath = public_path($category->name);
        if ($subCategory) {
            $destinationPath .= '/' . $subCategory->name;
        }
        if ($yearCategory) {
            $destinationPath .= '/' . $yearCategory->name;
        }

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
                'path' => $category->name . ($subCategory ? '/' . $subCategory->name : '') . ($yearCategory ? '/' . $yearCategory->name : '') . '/' . $fileName,
                'parent_id' => $category->id,
            ]);

            $importedFiles[] = $fileRecord;
        }

        return response()->json([
            'status' => true,
            'message' => 'Import thành công.',
            'imported_files' => $importedFiles,
            'existing_files' => $existingFiles,
        ]);
    }
}
