<?php

namespace Boilerplate\Media\Factories;

use Boilerplate\Media\Enums\MediaCollectionType;
use Boilerplate\Media\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Boilerplate\Media\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'collection_name' => MediaCollectionType::UNASSIGNED,
            'name' => 'test',
            'file_name' => 'test.png',
            'mime_type' => 'image/png',
            'disk' => 'public',
            'size' => 1,
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
        ];
    }
}
