<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return !empty(auth()->user()) && auth()->user()->id === $this->status->user_id;
    }

    public function rules(): array
    {

        return [
            'name' => [
                    'required',
                    'string',
                    Rule::unique('statuses')->where(function($query)  {
                        $query->where(['name' => request()->name, 'user_id' => auth()->user()->id])
                            ->whereNot(['id' => $this->status->id]);

                    })
                ],
            'position' => 'required|integer|gt:0'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Status name is required.',
            'name.string' => 'Status name must be text.',
            'name.unique' => 'Status name already used.',
            'position.required' => 'Position is required.',
            'position,integer' => 'Position must be a number',
            'position.gt' => 'Position must be a number greater than zero',
        ];
    }
}
