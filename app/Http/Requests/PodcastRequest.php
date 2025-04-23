<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PodcastRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|url|max:255',
            'author_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'boolean',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
        ];

        // For update requests, make slug unique except for the current model
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('podcasts')->ignore($this->podcast)
            ];
        } else {
            $rules['slug'] = 'required|string|max:255|unique:podcasts';
        }

        return $rules;
    }
}