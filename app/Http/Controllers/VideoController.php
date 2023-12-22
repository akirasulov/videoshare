<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoDestroyRequest;
use App\Http\Requests\VideoShowRequest;
use App\Http\Requests\VideoStoreRequest;
use App\Http\Requests\VideoUpdateRequest;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;

class VideoController extends Controller
{

    public function __construct(private VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function index(Request $request)
    {
        $videos = $this->videoService->index($request);

        return inertia()->render('Videos/Index', [
            'videos' => $videos,
        ]);
    }

    public function create()
    {
        return $this->videoService->create();
    }

    public function show(VideoShowRequest $request, Video $video)
    {
        $video = $this->videoService->show($request, $video);

        return inertia()->render('Videos/Show', [
            'video' => $video,
        ]);
    }

    public function store(VideoStoreRequest $request)
    {
        $this->videoService->store($request);

        return redirect()->route('videos.index');
    }

    public function update(VideoUpdateRequest $request, Video $video)
    {
        $this->videoService->update($request, $video);

        return back();
    }

    public function view(Video $video)
    {
        $video = $this->videoService->view($video);

        return inertia()->render('Videos/View', [
            'video' => $video,
        ]);
    }

    public function destroy(VideoDestroyRequest $request, Video $video)
    {
        $this->videoService->destroy($request, $video);

        return redirect()->route('videos.index');
    }
}
