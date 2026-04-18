<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->with(['children' => function($query) {
                $query->where('is_active', true);
            }])
            ->whereNull('parent_id')
            ->get();
            
        return response()->json($categories);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::with(['children', 'products', 'menuItems'])->findOrFail($id);
        return response()->json($category);
    }
}
