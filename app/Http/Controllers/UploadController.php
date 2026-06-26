<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $file = $request->file('image');
        $filename = Str::uuid() . '.webp';
        $path = 'uploads/' . $filename;

        try {
            $img = match ($file->getMimeType()) {
                'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()),
                'image/png'  => @imagecreatefrompng($file->getRealPath()),
                'image/webp' => @imagecreatefromwebp($file->getRealPath()),
                'image/gif'  => @imagecreatefromgif($file->getRealPath()),
                default      => null,
            };

            if (!$img) {
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = Str::uuid() . '.' . $ext;
                $path = 'uploads/' . $filename;
                Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            } else {
                if ($file->getMimeType() === 'image/png') {
                    imagepalettetotruecolor($img);
                }
                ob_start();
                imagewebp($img, null, 80);
                $webpData = ob_get_clean();
                imagedestroy($img);

                Storage::disk('public')->put($path, $webpData);
            }

            $imageUrl = url('storage/' . $path);
            return response()->json(['imageUrl' => $imageUrl]);
        } catch (\Exception $e) {
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = Str::uuid() . '.' . $ext;
            $path = 'uploads/' . $filename;
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            $imageUrl = url('storage/' . $path);
            return response()->json(['imageUrl' => $imageUrl]);
        }
    }
}
