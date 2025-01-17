<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FileSystem;
use Illuminate\Support\Str;
use App\Models\Admin;

class FilesController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = auth('admin')->user();
    }

    public function import(Request $request, $categorySlug, $subCategorySlug = null, $yearSlug = null)
    {
        $category = Str::slug($categorySlug . "manage");
        if (!$this->hasPermission($this->user, $category)) {
            abort(403, "No permission");
        }
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file'
        ]);

        $files = $request->file('files');

        $category = Category::where('slug', $categorySlug)->first();
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục không tồn tại.',
            ], 404);
        }

        $subCategory = $subCategorySlug ? Category::where('slug', $subCategorySlug)->where('parent_id', $category->id)->first() : null;


        if ($subCategorySlug && !$subCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục không tồn tại.',
            ], 404);
            $category = Str::slug($categorySlug . $subCategorySlug . "manage");
            if (!$this->hasPermission($this->user, $category)) {
                abort(403, "No permission");
            }
        }

        $yearCategory = null;
        if ($yearSlug) {
            $yearCategory = Category::where('slug', $yearSlug)
                ->where('parent_id', $subCategory ? $subCategory->id : $category->id)
                ->first();

            if ($yearCategory) {
                $categoryes = Str::slug($categorySlug . $subCategorySlug . $yearSlug . "manage");
                if (!$this->hasPermission($this->user, $categoryes)) {
                    abort(403, "No permission");
                }
            }

            if (!$yearCategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục không tồn tại.',
                ], 404);
            }
        }

        $hasSubCategory = Category::where('parent_id', $category->id)->exists();
        if ($hasSubCategory && !$subCategorySlug) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể import vào danh mục cha vì danh mục này có danh mục con.',
            ], 400);
        }

        if ($subCategory) {
            $hasYearCategory = Category::where('parent_id', $subCategory->id)->exists();
            if ($hasYearCategory && !$yearSlug) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể import vào danh mục con vì danh mục này có danh mục năm. Vui lòng chỉ định danh mục năm.',
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
            if ($yearCategory) {
                $categoryId = $yearCategory->id;
            }

            $fileRecord = File::create([
                'name' => $fileName,
                'path' => $category->name .
                    ($subCategory ? '/' . $subCategory->name : '') .
                    ($yearSlug ? '/' . $yearSlug : '') .
                    '/' . $fileName,
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

    public function view(Request $request, $categorySlug, $subCategorySlug = null, $yearSlug = null)
    {
        $categorySlugNormalized = Str::slug($categorySlug . "export");
        if (!$this->hasPermission($this->user, $categorySlugNormalized)) {
            abort(403, "No permission for category");
        }

        $category = Category::where('slug', $categorySlug)->first();
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục không tồn tại.',
            ], 404);
        }

        $subCategory = $subCategorySlug ? Category::where('slug', $subCategorySlug)->where('parent_id', $category->id)->first() : null;
        if ($subCategorySlug && !$subCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục con không tồn tại.',
            ], 404);
        }

        if ($subCategorySlug) {
            $subCategorySlugNormalized = Str::slug($categorySlug . $subCategorySlug . "export");
            if (!$this->hasPermission($this->user, $subCategorySlugNormalized)) {
                abort(403, "No permission for subcategory");
            }
        }

        $yearCategory = null;
        if ($yearSlug) {
            $yearSlugNormalized = Str::slug($categorySlug . $subCategorySlug . $yearSlug . "export");
            if (!$this->hasPermission($this->user, $yearSlugNormalized)) {
                abort(403, "No permission for year category");
            }

            $yearCategory = Category::where('slug', $yearSlug)
                ->where('parent_id', $subCategory ? $subCategory->id : $category->id)
                ->first();

            if (!$yearCategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục năm không tồn tại.',
                ], 404);
            }
        }

        $directoryPath = public_path($category->name);
        if ($subCategory) {
            $directoryPath .= '/' . $subCategory->name;
        }
        if ($yearSlug) {
            $directoryPath .= '/' . $yearSlug;
        }

        if (!FileSystem::exists($directoryPath)) {
            return response()->json([
                'status' => false,
                'message' => 'Không có file nào để export.',
            ], 404);
        }

        $files = FileSystem::allFiles($directoryPath);
        $downloadUrls = [];

        foreach ($files as $file) {
            $relativePath = str_replace(public_path(), '', $file->getRealPath());
            $downloadUrls[] = url($relativePath);
        }

        return response()->json([
            'status' => true,
            'message' => 'Danh sách file đã được tạo.',
            'files' => $downloadUrls,
        ]);
    }

    public function delete($id)
    {
        $file = File::find($id);
        if (!$file) {
            return response()->json([
                'status' => false,
                'message' => 'File không tồn tại.',
            ], 404);
        }

        $category = Category::find($file->category_id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục không tồn tại.',
            ], 404);
        }

        $slugPath = $category->slug;
        $subCategory = Category::where('parent_id', $category->id)->first();

        if ($subCategory) {
            $slugPath .= $subCategory->slug;

            $yearCategory = Category::where('parent_id', $subCategory->id)->first();
            if ($yearCategory) {
                $slugPath .= $yearCategory->slug;
            }
        }

        $permissionSlug = $slugPath . "manage";
        echo $permissionSlug; // In ra để kiểm tra
        //$file->delete();

        // return response()->json([
        //     'status' => true,
        //     'message' => 'Xóa file thành công.',
        // ]);
    }


    public function hasPermission(Admin $admin, $slug)
    {
        $normalizedSlug = Str::slug($slug);

        $permissions = $admin->roles->flatMap(function ($role) {
            return $role->permissions->pluck('slug');
        });

        $normalizedPermissions = $permissions->map(function ($permission) {
            return Str::slug($permission);
        });

        return $normalizedPermissions->contains($normalizedSlug);
    }
}
