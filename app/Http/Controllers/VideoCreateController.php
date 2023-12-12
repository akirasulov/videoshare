<?php

namespace App\Http\Controllers;

class VideoCreateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return inertia()->render('Videos/Create');
    }
}
