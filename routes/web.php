<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('viewer');

// Proxy untuk stream file PLY dari GitHub Releases
Route::get('/model/{filename}', function (string $filename) {
    $allowed = ['splat_sfmmvs.ply', 'splat_3dgs.ply', 'splat_georefgs.ply'];
    if (!in_array($filename, $allowed)) abort(404);

    $url = "https://github.com/giscgsplat-success/3dgs-geoviewer/releases/download/v1.0/{$filename}";

    $context = stream_context_create(['http' => [
        'follow_location' => true,
        'user_agent' => 'Mozilla/5.0',
    ]]);

    $stream = fopen($url, 'rb', false, $context);
    if (!$stream) abort(500, 'Gagal fetch dari GitHub');

    return response()->stream(function () use ($stream) {
        while (!feof($stream)) {
            echo fread($stream, 65536);
            flush();
        }
        fclose($stream);
    }, 200, [
        'Content-Type'                => 'application/octet-stream',
        'Access-Control-Allow-Origin' => '*',
        'Cache-Control'               => 'public, max-age=86400',
    ]);
})->where('filename', '.+\.ply$');

});

