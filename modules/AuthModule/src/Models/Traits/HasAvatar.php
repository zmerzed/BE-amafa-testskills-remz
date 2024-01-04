<?php

namespace Boilerplate\Auth\Models\Traits;

use Boilerplate\Media\Enums\MediaCollectionType;
use Boilerplate\Media\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\InteractsWithMedia;

trait HasAvatar
{
    use InteractsWithMedia;

    public function avatar(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', MediaCollectionType::AVATAR);
    }

    /*
    |--------------------------------------------------------------------------
    | Media Collections
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionType::AVATAR)
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')->width(254);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Remove the avatar media.
     *
     * @return void
     */
    public function removeAvatar(): void
    {
        $this->touch();

        $this->avatar()->delete();
    }

    /**
     * Set the avatar of the user
     */
    public function setAvatar(UploadedFile $file): Media
    {
        // Hashing file name
        $name = md5(uniqid(self::class . $this->getKey(), true));
        $fileName = $name . '.' . $file->extension();

        $this->touch();

        return $this->addMedia($file)
            ->usingName($name)
            ->usingFileName($fileName)
            ->toMediaCollection(MediaCollectionType::AVATAR);
    }

    /**
     * Set the avatar using a provided media id.
     * Media Id should belong to unasigned Type.
     */
    public function setAvatarByMediaId(int $mediaId): Media
    {
        $instance = $this;

        $media = Media::onlyUnassigned()->findOrFail($mediaId);

        $this->touch();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $media->move($instance, MediaCollectionType::AVATAR);
    }
}
