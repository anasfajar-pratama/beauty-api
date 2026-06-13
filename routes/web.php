<?php

use Illuminate\Support\Facades\Route;

// Fallback untuk SPA routing — semua non-API request diarahkan ke index.html
// (Aktifkan jika React dan Laravel berada di domain yang sama)
Route::get('/{any}', function () {
    return response()->json(['message' => 'Lumiere API is running.']);
})->where('any', '.*');
