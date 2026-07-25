<?php

use Illuminate\Support\Facades\Route;

/*
 * No product UI lives here — mobile talks to /api/v1/* directly and the
 * admin panel is Filament (/liora_change, ships its own bundled assets). A
 * plain JSON response avoids depending on a Vite/Node build (@vite() in the
 * default `welcome` view) purely to render an unused splash page.
 */
Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'status' => 'ok',
        'docs' => 'See docs/mvp/05-api-contract.md',
    ]);
});
