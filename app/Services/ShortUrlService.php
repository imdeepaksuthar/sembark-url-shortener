<?php

namespace App\Services;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ShortUrlService
{
     public function create(User $user, string $originalUrl): ShortUrl
     {
          Gate::forUser($user)->authorize('create', ShortUrl::class);

          do {
               $code = Str::random(7);
          } while (ShortUrl::where('code', $code)->exists());

          return ShortUrl::create([
               'company_id' => $user->company_id,
               'user_id' => $user->id,
               'code' => $code,
               'original_url' => $originalUrl,
          ]);
     }
}