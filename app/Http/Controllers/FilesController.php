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

        $hasSubCategory = Category::where('parent_id', $category->id)->exists();
        if ($hasSubCategory && !$subCategorySlug) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể import vào danh mục cha vì danh mục này có danh mục con.',
            ], 400);
        }

        $subCategory = $subCategorySlug ? Category::where('slug', $subCategorySlug)->where('parent_id', $category->id)->first() : null;
        if ($subCategory) {
            $hasYearCategory = Category::where('parent_id', $subCategory->id)->exists();
            if ($hasYearCategory && !$yearSlug) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể import vào danh mục con vì danh mục này có danh mục năm. Vui lòng chỉ định danh mục năm.',
                ], 400);
            }
        }

        if ($yearSlug) {
            $yearCategory = Category::where('slug', $yearSlug)->where('parent_id', $subCategory->id ?? null)->first();

            if (!$yearCategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục năm không hợp lệ. Nó phải là danh mục con của danh mục con.',
                ], 400);
            }
        }

        $destinationPath = public_path($category->name);
        if ($subCategory) {
            $destinationPath .= '/' . $subCategory->name;
        }
        if ($yearSlug) {
            $destinationPath .= '/' . $yearSlug;
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
            $categoryId = $category->id;
            if ($subCategory) {
                $categoryId = $subCategory->id;
            }
            if ($yearSlug) {
                $yearCategory = Category::where('slug', $yearSlug)->where('parent_id', $subCategory ? $subCategory->id : $category->id)->first();

                if ($yearCategory) {
                    $categoryId = $yearCategory->id;
                }
            }
            $fileRecord = File::create([
                'name' => $fileName,
                'path' => $category->name . ($subCategory ? '/' . $subCategory->name : '') . ($yearSlug ? '/' . $yearSlug : '') . '/' . $fileName,
                'category_id' => $categoryId,
            ]);

            $importedFiles[] = $fileRecord;
        }
        if ($existingFiles) {
            return response()->json([
                'status' => false,
                'message' => 'File đã tồn tại',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Import thành công.',
            'imported_files' => $importedFiles,
        ]);
    }
}
