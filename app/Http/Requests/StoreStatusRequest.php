<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !empty(auth()->user());
    }

    public function rules(): array
    {

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('statuses')->where(fn($query) => $query->where(['name' => request()->name, 'user_id' => auth()->user()->id]))],
            'position' => 'required|integer|gt:0'
        ];
    }
}
