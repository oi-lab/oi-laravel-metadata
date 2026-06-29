<?php

namespace OiLab\OiLaravelMetadata\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Models\Metadata;
use OiLab\OiLaravelMetadata\Support\SettingResolver;

/**
 * MetaService
 *
 * Reads, writes, and renders the SEO metadata of a model. The model must use
 * the HasMetadata trait (or otherwise expose a `metadata` MorphOne relation).
 */
class MetaService
{
    public function __construct(protected SettingResolver $settings) {}

    /**
     * Get the metadata record attached to a model, if any.
     */
    public function forModel(Model $model): ?Metadata
    {
        /** @var Metadata|null $metadata */
        $metadata = $model->metadata()->first();

        return $metadata;
    }

    /**
     * Build a MetadataData object from a model's stored metadata.
     */
    public function toData(Model $model): MetadataData
    {
        $metadata = $this->forModel($model);

        if ($metadata === null) {
            return new MetadataData;
        }

        return MetadataData::from($metadata);
    }

    /**
     * Create or update the metadata record of a model from a data object.
     */
    public function update(Model $model, MetadataData $data): Metadata
    {
        /** @var Metadata $metadata */
        $metadata = $model->metadata()->updateOrCreate([], [
            'title' => $data->title,
            'description' => $data->description,
            'keywords' => $data->keywords,
            'author' => $data->author,
            'copyright' => $data->copyright,
            'language' => $data->language,
            'revisit_after' => $data->revisit_after,
            'robots' => $data->robots,
            'googlebot' => $data->googlebot,
        ]);

        return $metadata;
    }

    /**
     * Render the metadata of a model (or a data object) as HTML meta tags.
     */
    public function render(Model|MetadataData|null $source = null): HtmlString
    {
        $data = $source instanceof MetadataData
            ? $source
            : ($source instanceof Model ? $this->toData($source) : new MetadataData);

        $tags = [];

        $this->appendTag($tags, 'name', 'description', $data->description);
        $this->appendTag($tags, 'name', 'keywords', $data->keywords === [] ? null : implode(', ', $data->keywords));
        $this->appendTag($tags, 'name', 'author', $data->author);
        $this->appendTag($tags, 'name', 'copyright', $data->copyright);
        $this->appendTag(
            $tags,
            'name',
            'language',
            $data->language ?? config('oi-laravel-metadata.defaults.language')
        );
        $this->appendTag(
            $tags,
            'name',
            'revisit-after',
            $data->revisit_after ?? config('oi-laravel-metadata.defaults.revisit_after')
        );
        $this->appendTag(
            $tags,
            'name',
            'robots',
            $data->robots ?? $this->settings->get('METADATA_ROBOTS', config('oi-laravel-metadata.defaults.robots'))
        );
        $this->appendTag(
            $tags,
            'name',
            'googlebot',
            $data->googlebot ?? $this->settings->get('METADATA_GOOGLE_BOT')
        );

        foreach ($this->verificationTags() as $line) {
            $tags[] = $line;
        }

        return new HtmlString(implode("\n", $tags));
    }

    /**
     * Build the site-wide verification meta tags resolved from settings.
     *
     * @return list<string>
     */
    protected function verificationTags(): array
    {
        $tags = [];

        $this->appendTag(
            $tags,
            'name',
            'google-site-verification',
            $this->blankToNull($this->settings->get('METADATA_GOOGLE_SITE_VERIFICATION'))
        );
        $this->appendTag(
            $tags,
            'name',
            'google',
            $this->blankToNull($this->settings->get('METADATA_GOOGLE'))
        );

        return $tags;
    }

    /**
     * Append a meta tag line when the value is not empty.
     *
     * @param  list<string>  $tags
     */
    protected function appendTag(array &$tags, string $attribute, string $key, ?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $tags[] = sprintf(
            '<meta %s="%s" content="%s">',
            $attribute,
            e($key),
            e($value)
        );
    }

    /**
     * Normalize empty strings to null.
     */
    protected function blankToNull(?string $value): ?string
    {
        return ($value === null || $value === '') ? null : $value;
    }
}
