<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $file      = $request->file('image');
        $origName  = $file->getClientOriginalName();
        $ext       = $file->getClientOriginalExtension();

        if (!$ext) {
            $mime = $file->getMimeType();
            $map = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
            ];
            $ext = $map[$mime] ?? 'jpg';
        }

        $filename  = Str::uuid() . '.' . $ext;
        $path      = $file->storeAs('uploads', $filename, 'public');

        if (!$path) {
            return response()->json(['message' => 'Gagal menyimpan file'], 500);
        }

        $imageUrl = url('storage/' . $path);

        return response()->json(['imageUrl' => $imageUrl]);
    }
}
