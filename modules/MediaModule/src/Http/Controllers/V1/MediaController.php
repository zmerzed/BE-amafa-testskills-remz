<?php

namespace Boilerplate\Media\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Boilerplate\Auth\Models\User;
use Boilerplate\Media\Enums\MediaCollectionType;
use Boilerplate\Media\Http\Resources\MediaResource;
use Boilerplate\Media\Models\Media;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use Boilerplate\Media\Http\Requests\StoreMediaRequest;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')
            ->except('imageFactory');
        $this->authorizeResource(Media::class, 'media');
    }

    public function store(StoreMediaRequest $request): MediaResource
    {
        /** @var User $user */
        $user = $request->user();

        $media = $user->addMediaFromRequest('file')
            ->toMediaCollection(MediaCollectionType::UNASSIGNED);

        return MediaResource::make($media);
    }

    public function show(Media $media): MediaResource
    {
        return MediaResource::make($media);
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json([], Response::HTTP_OK);
    }

    public function imageFactory(Request $request, Filesystem $filesystem, Media $media): mixed
    {
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory($request),
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'source_path_prefix' => 'public',
            'cache_path_prefix' => 'cache',
        ]);

        $urlGenerator = UrlGeneratorFactory::createForMedia($media);

        return $server->getImageResponse($urlGenerator->getPathRelativeToRoot(), $request->all());
    }
}
