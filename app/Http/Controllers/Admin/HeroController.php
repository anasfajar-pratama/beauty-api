<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    public function index()
    {
        return response()->json(Hero::orderBy('sort_order')->orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:brand,product,event',
            'brand_key' => 'nullable|string|in:blisera,fokka,pijar_nala',
            'product_type' => 'nullable|string|in:featured,promo,new',
            'theme' => 'required|string|in:rose,sky,slate,amber,emerald',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:500',
            'hero_image' => 'nullable|string|max:500',
            'logo' => 'nullable|string|max:500',
            'logo_style' => 'nullable|string|in:circle,rounded,square',
            'label' => 'nullable|string|max:255',
            'label_color' => 'nullable|string|max:50',
            'event_date' => 'nullable|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if (!isset($data['sort_order'])) {
            $max = Hero::max('sort_order');
            $data['sort_order'] = ($max ?? -1) + 1;
        }

        $hero = Hero::create($data);
        return response()->json($hero, 201);
    }

    public function show($id)
    {
        $hero = Hero::findOrFail($id);
        return response()->json($hero);
    }

    public function update(Request $request, $id)
    {
        $hero = Hero::findOrFail($id);

        $data = $request->validate([
            'type' => 'sometimes|in:brand,product,event',
            'brand_key' => 'nullable|string|in:blisera,fokka,pijar_nala',
            'product_type' => 'nullable|string|in:featured,promo,new',
            'theme' => 'sometimes|string|in:rose,sky,slate,amber,emerald',
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:500',
            'hero_image' => 'nullable|string|max:500',
            'logo' => 'nullable|string|max:500',
            'logo_style' => 'nullable|string|in:circle,rounded,square',
            'label' => 'nullable|string|max:255',
            'label_color' => 'nullable|string|max:50',
            'event_date' => 'nullable|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $hero->update($data);
        return response()->json($hero);
    }

    public function destroy($id)
    {
        $hero = Hero::findOrFail($id);
        $hero->delete();
        return response()->json(['message' => 'Hero berhasil dihapus']);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:heroes,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->orders as $item) {
            Hero::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Urutan hero berhasil diperbarui']);
    }

    public function publicIndex()
    {
        $heroes = Hero::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json($heroes);
    }
}
