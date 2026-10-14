<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    public function show(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();

        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->original_url);
    }
}