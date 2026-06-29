<?php

namespace OiLab\OiLaravelMetadata\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * RobotsRule
 *
 * Validates that a value is a comma-separated list of recognised robots
 * directives (e.g. `index, follow`, `noindex, nofollow`, `max-snippet:-1`).
 */
class RobotsRule implements ValidationRule
{
    /**
     * The recognised robots directives.
     *
     * @var list<string>
     */
    protected array $directives = [
        'index',
        'noindex',
        'follow',
        'nofollow',
        'all',
        'none',
        'noarchive',
        'nosnippet',
        'noimageindex',
        'notranslate',
        'nocache',
    ];

    /**
     * Directives that accept a `:value` argument.
     *
     * @var list<string>
     */
    protected array $parameterized = [
        'max-snippet',
        'max-image-preview',
        'max-video-preview',
        'unavailable_after',
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid robots directive string.');

            return;
        }

        foreach (explode(',', $value) as $token) {
            $token = trim($token);

            if ($token === '') {
                continue;
            }

            if (! $this->isValidToken($token)) {
                $fail("The :attribute contains an invalid robots directive: {$token}.");

                return;
            }
        }
    }

    /**
     * Determine whether a single token is a valid directive.
     */
    protected function isValidToken(string $token): bool
    {
        if (in_array(strtolower($token), $this->directives, true)) {
            return true;
        }

        $name = strtolower(explode(':', $token, 2)[0]);

        return in_array($name, $this->parameterized, true);
    }
}
