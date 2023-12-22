<?php

namespace App\Services;

use App\Http\Requests\VideoDestroyRequest;
use App\Http\Requests\VideoShowRequest;
use App\Http\Requests\VideoStoreRequest;
use App\Http\Requests\VideoUpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoService
{
    public function index(Request $request)
    {
        return VideoResource::collection($request->user()->videos()->latest()->get());
    }

    public function create()
    {
        return inertia()->render('Videos/Create');
    }

    public function show(VideoShowRequest $request, Video $video)
    {
        return VideoResource::make($video);
    }

    public function store(VideoStoreRequest $request)
    {
        $request->user()->videos()->create(
            $request->only('title', 'description') + [
                'video_path' => $request->video->storePubliclyAs('videos', Str::uuid() . '.mp4', 'public'),
            ]
        );
    }

    public function update(VideoUpdateRequest $request, Video $video)
    {
        $video->update($request->only('title', 'description'));

    }

    public function view(Video $video)
    {
        return VideoResource::make($video);

    }

    public function destroy(VideoDestroyRequest $request, Video $video)
    {
        $video->delete();
    }
}
