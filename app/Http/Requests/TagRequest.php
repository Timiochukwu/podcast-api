<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // For update requests, make name and slug unique except for the current model
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tags')->ignore($this->tag)
                ],
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tags')->ignore($this->tag)
                ],
            ];
        }

        return [
            'name' => 'required|string|max:255|unique:tags',
            'slug' => 'required|string|max:255|unique:tags',
        ];
    }
}