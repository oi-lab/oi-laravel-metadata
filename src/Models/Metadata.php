<?php

namespace OiLab\OiLaravelMetadata\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OiLab\OiLaravelMetadata\Database\Factories\MetadataFactory;

/**
 * Metadata Model
 *
 * SEO metadata attached polymorphically to a single parent model. Each parent
 * may have at most one metadata record (enforced by a unique index on the
 * morph columns).
 *
 * @property int $id Primary key
 * @property string|null $title Document title
 * @property string|null $description Document description
 * @property list<string>|null $keywords Keyword list
 * @property string|null $author Document author
 * @property string|null $copyright Copyright notice
 * @property string|null $language ISO language code (e.g. fr, en)
 * @property string|null $revisit_after Crawler revisit hint
 * @property string|null $robots Robots directive
 * @property string|null $googlebot Googlebot directive
 * @property string $metable_type Type of the parent model (polymorphic)
 * @property int $metable_id ID of the parent model (polymorphic)
 */
class Metadata extends Model
{
    /** @use HasFactory<MetadataFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'metadata';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'keywords',
        'author',
        'copyright',
        'language',
        'revisit_after',
        'robots',
        'googlebot',
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
            'keywords' => 'array',
            'metable_id' => 'integer',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MetadataFactory::new();
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
