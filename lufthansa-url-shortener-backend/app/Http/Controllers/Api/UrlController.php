<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResolveRequest;
use App\Http\Requests\ShortenUrlRequest;
use App\Http\Requests\UpdateExpiryTimeRequest;
use App\Models\Url;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UrlController extends Controller
{
    public function shorten(ShortenUrlRequest $request)
    {
        // Check if the URL already exists
        $existingUrl = Url::where('original_url', $request->original_url)->first();

        if ($existingUrl) {
            if ($existingUrl->expiry_time > Carbon::now()) {
                // Update the expiry time
                $existingUrl->update([
                    'expiry_time' => Carbon::now()->addMinutes((int)config('urlshortener.default_expiry_time')),
                ]);

                return response()->json([
                    'short_url' => $existingUrl->short_url,
                    'is_old' => true
                ], 200);
            }
            $existingUrl->delete();
        }

        // Generate a unique short URL
        $short_url = url(Str::random(6));

        $url = Url::create([
            'original_url' => $request->original_url,
            'short_url' => $short_url,
            'expiry_time' => Carbon::now()->addMinutes((int)config('urlshortener.default_expiry_time')), // Default expiration time
            'access_count' => 0, 
        ]);

        return response()->json(['short_url' => $url->short_url], 201);
    }

    public function resolve(ResolveRequest $request)
    {
        
        $url = Url::where('short_url', $request->short_url)->first();

        if (!$url) {
            return response()->json(['message' => 'URL not found'], 404);
        }

        if ($url->expiry_time < Carbon::now()) {
            return response()->json(['message' => 'URL has expired'], 410);
        }

        // Increment the access count
        $url->increment('access_count');

        return response()->json(['original_url' => $url->original_url], 201);
    }

    public function updateExpiryTime(UpdateExpiryTimeRequest $request)
    {
        $url = Url::where('short_url', $request->short_url)->first();

        if (!$url) {
            return response()->json(['message' => 'URL not found'], 404);
        }

        // Update the expiration time
        $url->update([
            'expiry_time' => Carbon::now()->addMinutes($request->expiry_time),
        ]);

        return response()->json(['message' => 'Expiration time updated'], 200);
    }
}
