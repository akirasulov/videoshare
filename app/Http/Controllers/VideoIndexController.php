<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use Illuminate\Http\Request;

class VideoIndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return inertia()->render('Videos/Index', [
            'videos' => VideoResource::collection($request->user()->videos()->latest()->get()),
        ]);
    }
}
