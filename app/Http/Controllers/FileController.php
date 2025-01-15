<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function import(Request $request, $categorySlug, $subCategorySlug = null, $yearSlug = null)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file'
        ]);

        $files = $request->file('files');

        echo ($categorySlug);
    }
}
