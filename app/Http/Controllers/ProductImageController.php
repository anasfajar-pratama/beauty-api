<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $data = $request->validate([
            'imageUrl'  => 'required|string',
            'isPrimary' => 'nullable|boolean',
            'sortOrder' => 'nullable|integer',
        ]);

        if ($data['isPrimary'] ?? false) {
            $product->images()->update(['is_primary' => false]);
        }

        $image = $product->images()->create([
            'image_url'  => $data['imageUrl'],
            'is_primary' => $data['isPrimary'] ?? false,
            'sort_order' => $data['sortOrder'] ?? 0,
        ]);

        return response()->json($image, 201);
    }

    public function destroy($productId, $imageId)
    {
        $image = ProductImage::where('product_id', $productId)->findOrFail($imageId);
        $image->delete();
        return response()->json(['message' => 'Gambar dihapus']);
    }

    public function setPrimary($productId, $imageId)
    {
        $product = Product::findOrFail($productId);
        $product->images()->update(['is_primary' => false]);
        $image = ProductImage::where('product_id', $productId)->findOrFail($imageId);
        $image->update(['is_primary' => true]);
        return response()->json(['message' => 'Gambar utama diperbarui']);
    }
}
