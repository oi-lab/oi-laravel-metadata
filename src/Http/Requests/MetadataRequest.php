<?php

namespace OiLab\OiLaravelMetadata\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OiLab\OiLaravelMetadata\Rules\IsoLanguageRule;
use OiLab\OiLaravelMetadata\Rules\RobotsRule;

class MetadataRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'keywords' => ['nullable', 'array'],
            'keywords.*' => ['string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'copyright' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', new IsoLanguageRule],
            'revisit_after' => ['nullable', 'string', 'max:255'],
            'robots' => ['nullable', 'string', new RobotsRule],
            'googlebot' => ['nullable', 'string', new RobotsRule],
        ];
    }
}
