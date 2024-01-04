<?php

namespace Boilerplate\Media\Models;

use Boilerplate\Media\Enums\MediaCollectionType;
use Boilerplate\Media\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

/**
 * App\Models\Media
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $uuid
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property string|null $conversions_disk
 * @property int $size
 * @property array $manipulations
 * @property array $custom_properties
 * @property array|null $generated_conversions
 * @property array $responsive_images
 * @property int|null $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model $model
 * @method static MediaCollection|static[] all($columns = ['*'])
 * @method static MediaCollection|static[] get($columns = ['*'])
 * @method static Builder|Media newModelQuery()
 * @method static Builder|Media newQuery()
 * @method static Builder|Media onlyUnassigned()
 * @method static Builder|Media ordered()
 * @method static Builder|Media query()
 */
class Media extends BaseMedia
{
    use HasFactory;

    protected static function newFactory(): MediaFactory
    {
        return MediaFactory::new();
    }

    public function scopeOnlyUnassigned(Builder $query): void
    {
        $query->whereCollectionName(MediaCollectionType::UNASSIGNED);
    }

    public function isUnassigned(): bool
    {
        return $this->collection_name === MediaCollectionType::UNASSIGNED;
    }
}
