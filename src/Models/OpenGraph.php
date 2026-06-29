<?php

namespace OiLab\OiLaravelMetadata\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OiLab\OiLaravelMetadata\Database\Factories\OpenGraphFactory;

/**
 * OpenGraph Model
 *
 * Open Graph object attached polymorphically to a single parent model. Each
 * parent may have at most one Open Graph record (enforced by a unique index on
 * the morph columns).
 *
 * @property int $id Primary key
 * @property string|null $type Open Graph type (e.g. website, article)
 * @property string|null $title Open Graph title
 * @property string|null $description Open Graph description
 * @property string|null $url Canonical URL
 * @property array{url?: string, width?: int, height?: int}|null $image Image descriptor
 * @property string $metable_type Type of the parent model (polymorphic)
 * @property int $metable_id ID of the parent model (polymorphic)
 */
class OpenGraph extends Model
{
    /** @use HasFactory<OpenGraphFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'open_graphs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'title',
        'description',
        'url',
        'image',
        'metable_type',
        'metable_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'image' => 'array',
            'metable_id' => 'integer',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return OpenGraphFactory::new();
    }

    /**
     * Get the parent metable model.
     *
     * @return MorphTo<Model, $this>
     */
    public function metable(): MorphTo
    {
        return $this->morphTo();
    }
}
