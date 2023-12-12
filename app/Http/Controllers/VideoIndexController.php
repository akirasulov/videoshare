<?php

namespace App\Http\Controllers;

class VideoIndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return inertia()->render('Videos/Index');
    }
}
