<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMediaRequest;
use App\Http\Resources\Api\MediaResource;
use App\Services\MediaManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function __construct(private MediaManagerService $mediaService) {}

    public function index()
    {
        $files = $this->mediaService->getMediaList();
        return MediaResource::collection($files);
    }

    public function store(StoreMediaRequest $request)
    {
        $filePath = $this->mediaService->uploadMediaFile($request->file('file'));
        $mediaData = (object) [
            'name' => basename($filePath),
            'url' => url('storage/' . $filePath),
        ];
        return new MediaResource($mediaData);
    }

    public function show($filename)
    {
        $filePath = 'media/' . $filename;

        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $mediaData = (object) [
            'name' => $filename,
            'url' => url('storage/' . $filePath),
        ];

        return new MediaResource($mediaData);
    }

    public function destroy($filename)
    {
        $deleted = $this->mediaService->deleteMediaFile($filename);

        if ($deleted) {
            return response()->json(['message' => 'File deleted']);
        }

        return response()->json(['message' => 'File not found'], 404);
    }
}
