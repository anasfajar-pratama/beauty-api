<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function publicIndex(Request $request)
    {
        $query = Subcategory::query();
        if ($request->category) {
            $query->where('category', $request->category);
        }
        return response()->json($query->orderBy('id')->get());
    }

    public function index()
    {
        return response()->json(Subcategory::orderBy('category')->orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|in:Wanita,Pria,Anak',
            'name'     => 'required|string|max:255',
            'slug'     => 'required|string|max:255|unique:subcategories',
        ]);
        $sub = Subcategory::create($data);
        return response()->json($sub, 201);
    }

    public function update(Request $request, $id)
    {
        $sub = Subcategory::findOrFail($id);
        $data = $request->validate([
            'category' => 'sometimes|in:Wanita,Pria,Anak',
            'name'     => 'sometimes|string|max:255',
            'slug'     => 'sometimes|string|max:255|unique:subcategories,slug,' . $id,
        ]);
        $sub->update($data);
        return response()->json($sub);
    }

    public function destroy($id)
    {
        Subcategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Subkategori dihapus']);
    }
}
