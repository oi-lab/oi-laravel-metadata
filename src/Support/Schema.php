<?php

namespace OiLab\OiLaravelMetadata\Support;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Schema
 *
 * Lightweight, fluent builder for a single Schema.org node (a JSON-LD object).
 * Property setters are dynamic: any method call sets the matching schema.org
 * property, and nested Schema objects are resolved recursively on render.
 *
 * <code>
 * Schema::article()
 *     ->headline('Hello world')
 *     ->datePublished('2026-07-07')
 *     ->author(Schema::person()->name('Jane Doe'));
 * </code>
 *
 * @implements Arrayable<string, mixed>
 *
 * @method static headline(string $value)
 * @method static name(string $value)
 * @method static description(string $value)
 * @method static url(string $value)
 * @method static image(mixed $value)
 * @method static datePublished(string $value)
 * @method static dateModified(string $value)
 * @method static author(mixed $value)
 * @method static publisher(mixed $value)
 * @method static itemListElement(array<int, mixed> $value)
 */
class Schema implements Arrayable, JsonSerializable
{
    /**
     * The set schema.org properties, keyed by property name.
     *
     * @var array<string, mixed>
     */
    protected array $properties = [];

    final public function __construct(protected string $type) {}

    /**
     * Start a node of an arbitrary Schema.org type (e.g. `Recipe`, `Event`).
     */
    public static function type(string $type): static
    {
        return new static($type);
    }

    public static function article(): static
    {
        return new static('Article');
    }

    public static function newsArticle(): static
    {
        return new static('NewsArticle');
    }

    public static function blogPosting(): static
    {
        return new static('BlogPosting');
    }

    public static function webPage(): static
    {
        return new static('WebPage');
    }

    public static function webSite(): static
    {
        return new static('WebSite');
    }

    public static function organization(): static
    {
        return new static('Organization');
    }

    public static function person(): static
    {
        return new static('Person');
    }

    public static function imageObject(): static
    {
        return new static('ImageObject');
    }

    public static function breadcrumbList(): static
    {
        return new static('BreadcrumbList');
    }

    public static function listItem(): static
    {
        return new static('ListItem');
    }

    public static function product(): static
    {
        return new static('Product');
    }

    public static function offer(): static
    {
        return new static('Offer');
    }

    public static function faqPage(): static
    {
        return new static('FAQPage');
    }

    public static function question(): static
    {
        return new static('Question');
    }

    public static function answer(): static
    {
        return new static('Answer');
    }

    /**
     * Get the Schema.org `@type` of this node.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set a Schema.org property. Passing null removes it.
     */
    public function set(string $property, mixed $value): static
    {
        if ($value === null) {
            unset($this->properties[$property]);

            return $this;
        }

        $this->properties[$property] = $value;

        return $this;
    }

    /**
     * Set the node identifier (`@id`).
     */
    public function id(string $iri): static
    {
        return $this->set('@id', $iri);
    }

    /**
     * Dynamic setter: `->headline('x')` maps to `->set('headline', 'x')`.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): static
    {
        return $this->set($name, $arguments[0] ?? null);
    }

    /**
     * Render the node (and any nested nodes) as a plain array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = ['@type' => $this->type];

        foreach ($this->properties as $property => $value) {
            $result[$property] = $this->resolve($value);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Recursively resolve nested Schema nodes, arrayables and dates to scalars.
     */
    protected function resolve(mixed $value): mixed
    {
        if ($value instanceof Schema || $value instanceof Arrayable) {
            return $value->toArray();
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->resolve($item), $value);
        }

        return $value;
    }
}
