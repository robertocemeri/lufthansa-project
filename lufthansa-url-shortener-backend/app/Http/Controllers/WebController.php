<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Carbon\Carbon;

class WebController extends Controller
{
    public function redirect($shortUrl)
    {
        $url = Url::where('short_url', url($shortUrl))->first();

        // is found and not expired
        if (!$url || $url->expiry_time < Carbon::now()) {
            return response()->json(['message' => 'URL not found or expired'], 404);
        }

        $url->increment('access_count');

        return redirect($url->original_url);
    }
}
