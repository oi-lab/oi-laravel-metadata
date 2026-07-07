<?php

namespace OiLab\OiLaravelMetadata\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * SeoContext
 *
 * Holds the "current" SEO subject so the `@meta`, `@og`, and `@jsonLd` Blade
 * directives (and the services) can render without an explicit argument. Set it
 * once — typically in a controller (`Seo::for($page)`) or a view composer — and
 * every directive renders that model. When nothing is set explicitly, the
 * subject is auto-resolved from the current route's model binding.
 */
class SeoContext
{
    protected ?Model $subject = null;

    /**
     * Set (or clear) the current SEO subject.
     */
    public function for(?Model $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Clear the current SEO subject.
     */
    public function forget(): void
    {
        $this->subject = null;
    }

    /**
     * Whether a subject has been explicitly set.
     */
    public function has(): bool
    {
        return $this->subject !== null;
    }

    /**
     * Resolve the current subject, optionally requiring a given relation method.
     *
     * An explicit subject wins; otherwise, when auto-resolution is enabled, the
     * last route-bound model exposing the relation is used. Returns null when no
     * suitable subject is available (callers then render their defaults).
     */
    public function subject(?string $relation = null): ?Model
    {
        if ($this->subject !== null) {
            return $this->accepts($this->subject, $relation) ? $this->subject : null;
        }

        if (! config('oi-laravel-metadata.auto_resolve_subject', true)) {
            return null;
        }

        return $this->resolveFromRoute($relation);
    }

    /**
     * Find the last route-bound model that exposes the given relation.
     */
    protected function resolveFromRoute(?string $relation): ?Model
    {
        $route = request()?->route();

        if ($route === null) {
            return null;
        }

        foreach (array_reverse($route->parameters()) as $parameter) {
            if ($parameter instanceof Model && $this->accepts($parameter, $relation)) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * Whether a model can satisfy the requested relation.
     */
    protected function accepts(Model $model, ?string $relation): bool
    {
        return $relation === null || method_exists($model, $relation);
    }
}
