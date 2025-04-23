<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EpisodeRequest extends FormRequest
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
            'podcast_id' => 'required|exists:podcasts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_url' => 'required|url|max:255',
            'duration_in_seconds' => 'required|integer|min:1',
            'transcript' => 'nullable|string',
            'is_featured' => 'boolean',
            'published_at' => 'required|date',
        ];

        // For update requests, make slug unique except for the current model
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('episodes')->ignore($this->episode)
            ];
        } else {
            $rules['slug'] = 'required|string|max:255|unique:episodes';
        }

        return $rules;
    }
}