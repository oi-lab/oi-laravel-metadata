<?php

namespace OiLab\OiLaravelMetadata\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use OiLab\OiLaravelMetadata\Data\OpenGraphData;
use OiLab\OiLaravelMetadata\Data\OpenGraphImageData;
use OiLab\OiLaravelMetadata\Models\OpenGraph;
use OiLab\OiLaravelMetadata\Support\SeoContext;
use OiLab\OiLaravelMetadata\Support\SettingResolver;

/**
 * OgService
 *
 * Reads, writes, and renders the Open Graph representation of a model. The
 * model must use the HasOpenGraph trait (or otherwise expose an `openGraph`
 * MorphOne relation).
 */
class OgService
{
    public function __construct(
        protected SettingResolver $settings,
        protected SeoContext $context,
    ) {}

    /**
     * Get the Open Graph record attached to a model, if any.
     */
    public function forModel(Model $model): ?OpenGraph
    {
        /** @var OpenGraph|null $openGraph */
        $openGraph = $model->openGraph()->first();

        return $openGraph;
    }

    /**
     * Build an OpenGraphData object from a model's stored Open Graph record.
     */
    public function toData(Model $model): OpenGraphData
    {
        $openGraph = $this->forModel($model);

        if ($openGraph === null) {
            return new OpenGraphData;
        }

        return new OpenGraphData(
            type: $openGraph->type,
            title: $openGraph->title,
            description: $openGraph->description,
            url: $openGraph->url,
            image: $openGraph->image === null ? null : OpenGraphImageData::from($openGraph->image),
        );
    }

    /**
     * Create or update the Open Graph record of a model from a data object.
     */
    public function update(Model $model, OpenGraphData $data): OpenGraph
    {
        /** @var OpenGraph $openGraph */
        $openGraph = $model->openGraph()->updateOrCreate([], [
            'type' => $data->type ?? $this->settings->get('METADATA_OG_TYPE', config('oi-laravel-metadata.settings.defaults.METADATA_OG_TYPE')),
            'title' => $data->title,
            'description' => $data->description,
            'url' => $data->url,
            'image' => $data->image?->toArray(),
        ]);

        return $openGraph;
    }

    /**
     * Render the Open Graph data of a model (or a data object) as HTML meta tags.
     */
    public function render(Model|OpenGraphData|null $source = null): HtmlString
    {
        $source ??= $this->context->subject('openGraph');

        $data = $source instanceof OpenGraphData
            ? $source
            : ($source instanceof Model ? $this->toData($source) : new OpenGraphData);

        $tags = [];

        $this->appendTag(
            $tags,
            'og:type',
            $data->type ?? $this->settings->get('METADATA_OG_TYPE')
        );
        $this->appendTag($tags, 'og:title', $data->title);
        $this->appendTag($tags, 'og:description', $data->description);
        $this->appendTag($tags, 'og:url', $data->url);

        if ($data->image !== null) {
            $this->appendTag($tags, 'og:image', $data->image->url);
            $this->appendTag($tags, 'og:image:width', $data->image->width === null ? null : (string) $data->image->width);
            $this->appendTag($tags, 'og:image:height', $data->image->height === null ? null : (string) $data->image->height);
        }

        $this->appendTag($tags, 'og:locale', $this->settings->get('METADATA_OG_LOCALE'));
        $this->appendTag($tags, 'og:site_name', $this->blankToNull($this->settings->get('METADATA_OG_SITE_NAME')));
        $this->appendTag($tags, 'fb:app_id', $this->blankToNull($this->settings->get('METADATA_FACEBOOK_APP_ID')));

        return new HtmlString(implode("\n", $tags));
    }

    /**
     * Append an Open Graph meta tag line when the value is not empty.
     *
     * @param  list<string>  $tags
     */
    protected function appendTag(array &$tags, string $property, ?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $tags[] = sprintf(
            '<meta property="%s" content="%s">',
            e($property),
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
