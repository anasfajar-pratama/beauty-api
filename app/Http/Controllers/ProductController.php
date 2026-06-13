<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Public: list active products ordered by sort_order
    public function publicIndex()
    {
        $products = Product::orderBy('sort_order')->orderBy('id')->get()->map(function ($p) {
            return $this->format($p);
        });

        return response()->json($products);
    }

    // Public: single product by id
    public function publicShow($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($this->format($product));
    }

    // Admin: all products
    public function index()
    {
        $products = Product::orderBy('sort_order')->orderBy('id')->get()->map(function ($p) {
            return $this->formatAdmin($p);
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'required|in:Wanita,Pria,Anak',
            'tagline'    => 'required|string|max:255',
            'ingredients'=> 'nullable|string',
            'benefits'   => 'nullable|array',
            'howToUse'   => 'nullable|array',
            'imageUrl'   => 'nullable|string',
            'sortOrder'  => 'nullable|integer',
        ]);

        $product = Product::create([
            'name'        => $data['name'],
            'category'    => $data['category'],
            'tagline'     => $data['tagline'],
            'ingredients' => $data['ingredients'] ?? null,
            'benefits'    => json_encode(array_values(array_filter($data['benefits'] ?? []))),
            'how_to_use'  => json_encode(array_values(array_filter($data['howToUse'] ?? []))),
            'image_url'   => $data['imageUrl'] ?? null,
            'sort_order'  => $data['sortOrder'] ?? 0,
        ]);

        return response()->json($this->formatAdmin($product), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'category'   => 'sometimes|in:Wanita,Pria,Anak',
            'tagline'    => 'sometimes|string|max:255',
            'ingredients'=> 'nullable|string',
            'benefits'   => 'nullable|array',
            'howToUse'   => 'nullable|array',
            'imageUrl'   => 'nullable|string',
            'sortOrder'  => 'nullable|integer',
        ]);

        $product->update([
            'name'        => $data['name']        ?? $product->name,
            'category'    => $data['category']    ?? $product->category,
            'tagline'     => $data['tagline']      ?? $product->tagline,
            'ingredients' => $data['ingredients']  ?? $product->ingredients,
            'benefits'    => isset($data['benefits'])  ? json_encode(array_values(array_filter($data['benefits'])))  : $product->benefits,
            'how_to_use'  => isset($data['howToUse'])  ? json_encode(array_values(array_filter($data['howToUse'])))  : $product->how_to_use,
            'image_url'   => array_key_exists('imageUrl', $data)  ? $data['imageUrl']   : $product->image_url,
            'sort_order'  => $data['sortOrder']   ?? $product->sort_order,
        ]);

        return response()->json($this->formatAdmin($product->fresh()));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Produk dihapus']);
    }

    private function format(Product $p): array
    {
        return [
            'id'          => $p->id,
            'name'        => $p->name,
            'category'    => $p->category,
            'tagline'     => $p->tagline,
            'ingredients' => $p->ingredients,
            'benefits'    => json_decode($p->benefits ?? '[]', true),
            'how_to_use'  => json_decode($p->how_to_use ?? '[]', true),
            'image_url'   => $p->image_url,
        ];
    }

    private function formatAdmin(Product $p): array
    {
        return [
            'id'          => $p->id,
            'name'        => $p->name,
            'category'    => $p->category,
            'tagline'     => $p->tagline,
            'ingredients' => $p->ingredients,
            'benefits'    => json_decode($p->benefits ?? '[]', true),
            'howToUse'    => json_decode($p->how_to_use ?? '[]', true),
            'imageUrl'    => $p->image_url,
            'sortOrder'   => $p->sort_order,
        ];
    }
}
