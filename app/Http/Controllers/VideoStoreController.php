<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoStoreRequest;
use Illuminate\Support\Str;

class VideoStoreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(VideoStoreRequest $request)
    {
        $request->user()->videos()->create(
            $request->only('title', 'description') + [
                'video_path' => $request->video->storePubliclyAs('videos', Str::uuid() . '.mp4', 'public'),
            ]
        );

        return redirect()->route('videos.index');
    }
}
