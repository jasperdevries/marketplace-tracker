<?php

namespace App\Rules;

use App\Services\SteamApiService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\RequestException;

class SteamItemExists implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = new SteamApiService();
        try {
            $service->getPrice($value);
        } catch (RequestException) {
            $fail('This item does not exist on Steam.');
        }
    }
}
