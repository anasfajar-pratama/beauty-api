<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GalleryItem;

class GalleryController extends Controller
{
    // Public: only active gallery items
    public function publicIndex()
    {
        $items = GalleryItem::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn($g) => $this->format($g));
        return response()->json($items);
    }

    // Admin: all gallery items
    public function index()
    {
        $items = GalleryItem::orderBy('sort_order')->orderBy('id')->get()
            ->map(fn($g) => $this->formatAdmin($g));
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'imageUrl'    => 'required|string',
            'altText'     => 'nullable|string|max:255',
            'isActive'    => 'nullable|boolean',
            'sortOrder'   => 'nullable|string',
            'title'       => 'nullable|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $item = GalleryItem::create([
            'image_url'   => $data['imageUrl'],
            'alt_text'    => $data['altText'] ?? null,
            'is_active'   => $data['isActive'] ?? true,
            'sort_order'  => (int) ($data['sortOrder'] ?? 0),
            'title'       => $data['title'] ?? null,
            'tagline'     => $data['tagline'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($this->formatAdmin($item), 201);
    }

    public function update(Request $request, $id)
    {
        $item = GalleryItem::findOrFail($id);

        $data = $request->validate([
            'imageUrl'    => 'sometimes|string',
            'altText'     => 'nullable|string|max:255',
            'isActive'    => 'nullable|boolean',
            'sortOrder'   => 'nullable|string',
            'title'       => 'nullable|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $item->update([
            'image_url'   => $data['imageUrl']    ?? $item->image_url,
            'alt_text'    => array_key_exists('altText', $data)     ? $data['altText']     : $item->alt_text,
            'is_active'   => array_key_exists('isActive', $data)    ? $data['isActive']    : $item->is_active,
            'sort_order'  => isset($data['sortOrder']) ? (int) $data['sortOrder'] : $item->sort_order,
            'title'       => array_key_exists('title', $data)       ? $data['title']       : $item->title,
            'tagline'     => array_key_exists('tagline', $data)     ? $data['tagline']     : $item->tagline,
            'description' => array_key_exists('description', $data) ? $data['description'] : $item->description,
        ]);

        return response()->json($this->formatAdmin($item->fresh()));
    }

    public function destroy($id)
    {
        GalleryItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Gambar dihapus']);
    }

    private function format(GalleryItem $g): array
    {
        return [
            'id'          => $g->id,
            'imageUrl'    => $g->image_url,
            'altText'     => $g->alt_text,
            'title'       => $g->title,
            'tagline'     => $g->tagline,
            'description' => $g->description,
        ];
    }

    private function formatAdmin(GalleryItem $g): array
    {
        return [
            'id'          => $g->id,
            'imageUrl'    => $g->image_url,
            'altText'     => $g->alt_text,
            'isActive'    => (bool) $g->is_active,
            'sortOrder'   => (string) $g->sort_order,
            'title'       => $g->title,
            'tagline'     => $g->tagline,
            'description' => $g->description,
        ];
    }
}
