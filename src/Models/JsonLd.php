<?php

namespace OiLab\OiLaravelMetadata\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OiLab\OiLaravelMetadata\Database\Factories\JsonLdFactory;

/**
 * JsonLd Model
 *
 * JSON-LD structured data attached polymorphically to a single parent model.
 * Each parent may have at most one JSON-LD record (enforced by a unique index
 * on the morph columns); that record holds a list of Schema.org graphs, so a
 * page can still expose several structured-data objects at once.
 *
 * @property int $id Primary key
 * @property list<array<string, mixed>>|null $graphs Schema.org graphs (each rendered as its own script block)
 * @property string $metable_type Type of the parent model (polymorphic)
 * @property int $metable_id ID of the parent model (polymorphic)
 */
class JsonLd extends Model
{
    /** @use HasFactory<JsonLdFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'json_ld';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'graphs',
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
            'graphs' => 'array',
            'metable_id' => 'integer',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return JsonLdFactory::new();
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
