<?php

namespace OiLab\OiLaravelMetadata\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OpenGraphRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'array'],
            'image.url' => ['nullable', 'url', 'max:2048'],
            'image.width' => ['nullable', 'integer', 'min:1'],
            'image.height' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
