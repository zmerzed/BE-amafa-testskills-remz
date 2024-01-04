<?php

namespace Boilerplate\Media\Actions;

use Boilerplate\Media\Models\Interfaces\HasMedia;
use Boilerplate\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class SyncModelAttachments
{
    protected HasMedia $model;

    protected Collection $attachments;

    public function execute(HasMedia $model, Collection $attachments): void
    {
        $this->model = $model;
        $this->attachments = $attachments;

        $this->addUploadedFiles();
        $this->addFilesById();
        $this->removeMarkedFiles();
    }

    protected function addUploadedFiles(): void
    {
        $uploadedFile = $this->attachments->filter(function ($item) {
            return $item instanceof UploadedFile;
        });

        $uploadedFile->each(function ($file) {
            $this->model->addMedia($file)
                ->toMediaCollection($this->model->defaultCollectionName());
        });
    }

    /**
     * Handles the saving of files that was marked as unsigned.
     */
    protected function addFilesById(): void
    {
        $addedFiles = $this->attachments->filter(function ($item) {
            return !data_get($item, 'delete');
        })->pluck('id');

        if (count($addedFiles)) {
            Media::query()
                ->onlyUnassigned()
                ->whereIn('id', $addedFiles)
                ->get()
                ->each(function (Media $media) {
                    $media->move($this->model, $this->model->defaultCollectionName());
                });
        }
    }

    /**
     * Handles the deleting of files.
     */
    protected function removeMarkedFiles(): void
    {
        $removedFiles = $this->attachments->filter(function ($item) {
            return data_get($item, 'delete');
        })->pluck('id');

        if (count($removedFiles)) {
            $this->model->media()
                ->whereIn('id', $removedFiles)
                ->where('collection_name', $this->model->defaultCollectionName())
                ->delete();
        }
    }
}
