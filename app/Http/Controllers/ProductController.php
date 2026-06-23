<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function publicIndex(Request $request)
    {
        $query = Product::with(['subcategory', 'images']);

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->subcategory) {
            $query->whereHas('subcategory', fn($q) => $q->where('slug', $request->subcategory));
        }
        if ($request->is_promo) {
            $query->where('is_promo', true);
        }
        if ($request->is_new) {
            $query->where('is_new', true);
        }
        if ($request->is_featured) {
            $query->where('is_featured', true);
        }

        $products = $query->orderBy('sort_order')->orderBy('id')->get()->map(fn($p) => $this->format($p));
        return response()->json($products);
    }

    public function publicShow($id)
    {
        $product = Product::with(['subcategory', 'images'])->findOrFail($id);
        return response()->json($this->format($product));
    }

    public function index()
    {
        $products = Product::with(['subcategory', 'images'])->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn($p) => $this->formatAdmin($p));
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with(['subcategory', 'images'])->findOrFail($id);
        return response()->json($this->formatAdmin($product));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|in:Wanita,Pria,Anak',
            'subcategory_id'=> 'nullable|exists:subcategories,id',
            'tagline'       => 'required|string|max:255',
            'description'   => 'nullable|string',
            'ingredients'   => 'nullable|string',
            'benefits'      => 'nullable|array',
            'howToUse'      => 'nullable|array',
            'imageUrl'      => 'nullable|string',
            'sortOrder'     => 'nullable|integer',
            'isPromo'       => 'nullable|boolean',
            'isNew'         => 'nullable|boolean',
            'isFeatured'    => 'nullable|boolean',
            'weight'        => 'nullable|string',
            'dimensions'    => 'nullable|string',
            'bpomNumber'    => 'nullable|string',
            'certifications'=> 'nullable|array',
            'halalCertified'=> 'nullable|boolean',
            'warrantyInfo'  => 'nullable|string',
            'price'         => 'nullable|numeric',
        ]);

        $product = Product::create([
            'name'            => $data['name'],
            'category'        => $data['category'],
            'subcategory_id'  => $data['subcategory_id'] ?? null,
            'tagline'         => $data['tagline'],
            'description'     => $data['description'] ?? null,
            'ingredients'     => $data['ingredients'] ?? null,
            'benefits'        => isset($data['benefits']) ? json_encode(array_values(array_filter($data['benefits']))) : null,
            'how_to_use'      => isset($data['howToUse']) ? json_encode(array_values(array_filter($data['howToUse']))) : null,
            'image_url'       => $data['imageUrl'] ?? null,
            'sort_order'      => $data['sortOrder'] ?? 0,
            'is_promo'        => $data['isPromo'] ?? false,
            'is_new'          => $data['isNew'] ?? false,
            'is_featured'     => $data['isFeatured'] ?? false,
            'weight'          => $data['weight'] ?? null,
            'dimensions'      => $data['dimensions'] ?? null,
            'bpom_number'     => $data['bpomNumber'] ?? null,
            'certifications'  => isset($data['certifications']) ? json_encode($data['certifications']) : null,
            'halal_certified' => $data['halalCertified'] ?? false,
            'warranty_info'   => $data['warrantyInfo'] ?? null,
            'price'           => $data['price'] ?? null,
        ]);

        if (!empty($data['imageUrl'])) {
            $product->images()->create([
                'image_url' => $data['imageUrl'],
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        return response()->json($this->formatAdmin($product->fresh()->load(['subcategory', 'images'])), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('images')->findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'category'      => 'sometimes|in:Wanita,Pria,Anak',
            'subcategory_id'=> 'nullable|exists:subcategories,id',
            'tagline'       => 'sometimes|string|max:255',
            'description'   => 'nullable|string',
            'ingredients'   => 'nullable|string',
            'benefits'      => 'nullable|array',
            'howToUse'      => 'nullable|array',
            'imageUrl'      => 'nullable|string',
            'sortOrder'     => 'nullable|integer',
            'isPromo'       => 'nullable|boolean',
            'isNew'         => 'nullable|boolean',
            'isFeatured'    => 'nullable|boolean',
            'weight'        => 'nullable|string',
            'dimensions'    => 'nullable|string',
            'bpomNumber'    => 'nullable|string',
            'certifications'=> 'nullable|array',
            'halalCertified'=> 'nullable|boolean',
            'warrantyInfo'  => 'nullable|string',
            'price'         => 'nullable|numeric',
        ]);

        $updateData = [];
        foreach ([
            'name' => 'name', 'category' => 'category', 'tagline' => 'tagline',
            'description' => 'description', 'ingredients' => 'ingredients',
            'sortOrder' => 'sort_order', 'weight' => 'weight', 'dimensions' => 'dimensions',
            'bpomNumber' => 'bpom_number', 'warrantyInfo' => 'warranty_info',
            'isPromo' => 'is_promo', 'isNew' => 'is_new', 'isFeatured' => 'is_featured', 'halalCertified' => 'halal_certified',
            'price' => 'price',
        ] as $reqKey => $dbKey) {
            if (array_key_exists($reqKey, $data)) {
                $updateData[$dbKey] = $data[$reqKey];
            }
        }

        if (array_key_exists('subcategory_id', $data)) {
            $updateData['subcategory_id'] = $data['subcategory_id'];
        }

        if (isset($data['benefits'])) {
            $updateData['benefits'] = json_encode(array_values(array_filter($data['benefits'])));
        }
        if (isset($data['howToUse'])) {
            $updateData['how_to_use'] = json_encode(array_values(array_filter($data['howToUse'])));
        }
        if (isset($data['certifications'])) {
            $updateData['certifications'] = json_encode($data['certifications']);
        }

        $product->update($updateData);

        if (array_key_exists('imageUrl', $data)) {
            if ($data['imageUrl']) {
                $product->images()->where('is_primary', true)->update(['is_primary' => false]);
                $product->images()->create([
                    'image_url' => $data['imageUrl'],
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }
        }

        return response()->json($this->formatAdmin($product->fresh()->load(['subcategory', 'images'])));
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Produk dihapus']);
    }

    private function format(Product $p): array
    {
        return [
            'id'            => $p->id,
            'name'          => $p->name,
            'category'      => $p->category,
            'subcategory'   => $p->subcategory ? ['id' => $p->subcategory->id, 'name' => $p->subcategory->name, 'slug' => $p->subcategory->slug] : null,
            'tagline'       => $p->tagline,
            'description'   => $p->description,
            'ingredients'   => $p->ingredients,
            'benefits'      => json_decode($p->benefits ?? '[]', true),
            'howToUse'      => json_decode($p->how_to_use ?? '[]', true),
            'images'        => $p->images->map(fn($i) => ['id' => $i->id, 'imageUrl' => $i->image_url, 'isPrimary' => $i->is_primary])->values(),
            'imageUrl'      => $p->image_url,
            'isPromo'       => $p->is_promo,
            'isNew'         => $p->is_new,
            'isFeatured'    => $p->is_featured,
            'weight'        => $p->weight,
            'dimensions'    => $p->dimensions,
            'bpomNumber'    => $p->bpom_number,
            'certifications'=> json_decode($p->certifications ?? '[]', true),
            'halalCertified'=> $p->halal_certified,
            'warrantyInfo'  => $p->warranty_info,
            'price'         => $p->price,
        ];
    }

    private function formatAdmin(Product $p): array
    {
        return [
            'id'            => $p->id,
            'name'          => $p->name,
            'category'      => $p->category,
            'subcategory'   => $p->subcategory ? ['id' => $p->subcategory->id, 'name' => $p->subcategory->name, 'slug' => $p->subcategory->slug] : null,
            'tagline'       => $p->tagline,
            'description'   => $p->description,
            'ingredients'   => $p->ingredients,
            'benefits'      => json_decode($p->benefits ?? '[]', true),
            'howToUse'      => json_decode($p->how_to_use ?? '[]', true),
            'images'        => $p->images->map(fn($i) => ['id' => $i->id, 'imageUrl' => $i->image_url, 'isPrimary' => $i->is_primary, 'sortOrder' => $i->sort_order])->values(),
            'imageUrl'      => $p->image_url,
            'sortOrder'     => $p->sort_order,
            'isPromo'       => $p->is_promo,
            'isNew'         => $p->is_new,
            'isFeatured'    => $p->is_featured,
            'weight'        => $p->weight,
            'dimensions'    => $p->dimensions,
            'bpomNumber'    => $p->bpom_number,
            'certifications'=> json_decode($p->certifications ?? '[]', true),
            'halalCertified'=> $p->halal_certified,
            'warrantyInfo'  => $p->warranty_info,
            'price'         => $p->price,
        ];
    }
}
