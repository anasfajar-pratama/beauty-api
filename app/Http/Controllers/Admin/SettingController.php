<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return response()->json(Setting::orderBy('group')->orderBy('key')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
            'type' => 'required|in:string,text,boolean,json,image',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $setting = Setting::create($data);
        return response()->json($setting, 201);
    }

    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);
        $data = $request->validate([
            'value' => 'nullable|string',
            'type' => 'sometimes|in:string,text,boolean,json,image',
            'group' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $setting->update($data);
        return response()->json($setting);
    }

    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();
        return response()->json(['message' => 'Setting dihapus']);
    }

    public function publicIndex()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }
}
