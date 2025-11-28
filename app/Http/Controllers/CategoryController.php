<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,NULL,id,user_id,' . Auth::id(),
        ]);

        Category::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('status', 'Kategori berhasil ditambahkan!');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id === Auth::id()) {
            $category->delete();
        }

        return redirect()->back()->with('status', 'Kategori berhasil dihapus!');
    }
}
