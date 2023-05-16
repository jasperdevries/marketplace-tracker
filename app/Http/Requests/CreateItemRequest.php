<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'market_hash_name' => ['required', 'string', 'unique:items,market_hash_name'],
        ];
    }
}
