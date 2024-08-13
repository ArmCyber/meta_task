<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'init' => [
                'nullable',
                'boolean'
            ],
            'name' => [
                'required_with:init',
                'string',
                'max:255'
            ],
            'total_size' => [
                'required_with:init',
                'integer'
            ],
            'id' => [
                'required_without:init',
                'string',
                Rule::exists('assets', 'id')->where('is_completed', false),
            ],
            'chunk' => [
                'required',
                File::default()->max('1mb'),
            ]
        ];
    }
}
