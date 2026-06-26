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
            'section_kategori_title', 'section_kategori_subtitle',
            'section_unggulan_title', 'section_unggulan_subtitle',
            'section_promo_title', 'section_promo_subtitle',
            'section_terbaru_title', 'section_terbaru_subtitle',
            'section_features_title', 'section_features_subtitle',
            'section_testimonials_title', 'section_testimonials_subtitle',
            'about_title', 'about_text', 'about_text1', 'about_text2', 'about_quote', 'about_image',
            'newsletter_title', 'newsletter_subtitle',
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

    // About page — return all about_* keys
    public function publicAbout()
    {
        $rows = HomepageContent::where('key', 'like', 'about_%')->get();
        $result = [];
        foreach ($rows as $row) {
            $result[$row->key] = $row->value;
        }
        return response()->json($result);
    }

    // Admin: save about page content
    public function storeAbout(Request $request)
    {
        $allowed = [
            'about_hero_title', 'about_hero_subtitle',
            'about_story_heading', 'about_story_text', 'about_story_text1', 'about_story_text2', 'about_story_text3',
            'about_story_image',
            'about_values_title',
            'about_value_1_icon', 'about_value_1_title', 'about_value_1_desc',
            'about_value_2_icon', 'about_value_2_title', 'about_value_2_desc',
            'about_value_3_icon', 'about_value_3_title', 'about_value_3_desc',
            'about_value_4_icon', 'about_value_4_title', 'about_value_4_desc',
            'about_team_title',
            'about_team_1_image', 'about_team_1_name', 'about_team_1_role',
            'about_team_2_image', 'about_team_2_name', 'about_team_2_role',
            'about_team_3_image', 'about_team_3_name', 'about_team_3_role',
            'about_team_4_image', 'about_team_4_name', 'about_team_4_role',
            'about_permit_title',
            'about_permit_1_title', 'about_permit_1_url',
            'about_permit_2_title', 'about_permit_2_url',
            'about_permit_3_title', 'about_permit_3_url',
            'about_permit_4_title', 'about_permit_4_url',
            'about_permit_5_title', 'about_permit_5_url',
            'about_permit_6_title', 'about_permit_6_url',
        ];

        foreach ($allowed as $key) {
            if ($request->has($key)) {
                HomepageContent::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        return response()->json(['message' => 'Konten about berhasil disimpan']);
    }
}
