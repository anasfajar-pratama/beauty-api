<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomepageContent;

class HomepageContentController extends Controller
{
    // Public: return all content as key => value object
    public function publicIndex()
    {
        $rows = HomepageContent::all();
        $result = [];
        foreach ($rows as $row) {
            $result[$row->key] = $row->value;
        }
        return response()->json($result);
    }

    // Admin: save all content fields (POST with key-value object)
    public function store(Request $request)
    {
        $allowed = [
            'hero_title', 'hero_title_highlight', 'hero_subtitle',
            'about_title', 'about_text1', 'about_text2', 'about_quote',
            'newsletter_title', 'newsletter_subtitle',
            'brand_name', 'brand_tagline',
        ];

        foreach ($allowed as $key) {
            if ($request->has($key)) {
                HomepageContent::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        return response()->json(['message' => 'Konten homepage berhasil disimpan']);
    }
}
