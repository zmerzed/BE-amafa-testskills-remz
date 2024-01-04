<?php

namespace Boilerplate\Media\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * @var \Boilerplate\Media\Models\Media
     */
    public $resource;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'collection_name' => $this->collection_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'created_at' => $this->created_at,
            'url' => $this->getFullUrl(),
            'thumb_url' => $this->resource->hasGeneratedConversion('thumb') ? $this->resource->getFullUrl(
                'thumb'
            ) : $this->getFullUrl(),
            'responsive_url' => route('media.responsive', $this->id),
        ];
    }
}
