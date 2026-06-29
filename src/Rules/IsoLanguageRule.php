<?php

namespace OiLab\OiLaravelMetadata\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * IsoLanguageRule
 *
 * Validates that a value is a plausible ISO 639-1 language code, optionally
 * with an ISO 3166-1 region suffix (e.g. `fr`, `en`, `fr-FR`, `pt_BR`).
 */
class IsoLanguageRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || preg_match('/^[a-z]{2}([-_][A-Za-z]{2})?$/', $value) !== 1) {
            $fail('The :attribute must be a valid ISO language code (e.g. fr, en, fr-FR).');
        }
    }
}
