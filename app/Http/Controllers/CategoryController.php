<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function show($categorySlug, $subCategorySlug = null, $yearSlug = null)
    {
        try {
            $category = Category::where('slug', $categorySlug)->firstOrFail();

            $this->checkPermission($category->slug . '.manage');

            $files = [];

            if ($subCategorySlug) {
                $subCategory = Category::where('slug', $subCategorySlug)
                    ->where('parent_id', $category->id)
                    ->firstOrFail();

                $this->checkPermission($subCategory->slug . '.manage');

                if ($yearSlug) {
                    $yearCategory = Category::where('slug', $yearSlug)
                        ->where('parent_id', $subCategory->id)
                        ->firstOrFail();

                    $this->checkPermission($yearCategory->slug . '.manage');

                    $files = $yearCategory->files;
                } else {
                    $files = $subCategory->files;
                }
            } else {
                $files = $category->files;
            }

            return response()->json([
                'status' => true,
                'data' => $files,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    protected function checkPermission($permission)
    {
        $user = Auth::guard('admin')->user();

        if (!$user->role || !$user->role->permissions || $user->role->permissions->isEmpty()) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        $slugs = $user->role->permissions->pluck('slug')->map(function ($slug) {
            return Str::slug($slug);
        });

        if (!$slugs->contains(Str::slug($permission))) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
    }
}
