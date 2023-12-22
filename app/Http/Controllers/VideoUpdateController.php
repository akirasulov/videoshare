<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoUpdateRequest;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(VideoUpdateRequest $requst, Video $video)
    {
        $video->update($requst->only('title', 'description'));

        return back();
    }
}
