<?php

namespace App\Observers;

use App\Jobs\ConvertVideoFormat;
use App\Jobs\GenerateVideoPreviewImage;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoObserver
{
    /**
     * Handle the Video "creating" event.
     */
    public function creating(Video $video): void
    {
        $video->uuid = Str::uuid();
    }

    /**
     * Handle the Video "created" event.
     */
    public function created(Video $video): void
    {
        // GenerateVideoPreviewImage::dispatchSync($video);
        GenerateVideoPreviewImage::dispatch($video);
        ConvertVideoFormat::dispatch($video);
    }

    /**
     * Handle the Video "updated" event.
     */
    public function updated(Video $video): void
    {
        //
    }

    /**
     * Handle the Video "deleted" event.
     */
    public function deleted(Video $video): void
    {
        Storage::disk('public')
            ->delete(
                $video->only('video_path', 'still_path')
            );
    }

    /**
     * Handle the Video "restored" event.
     */
    public function restored(Video $video): void
    {
        //
    }

    /**
     * Handle the Video "force deleted" event.
     */
    public function forceDeleted(Video $video): void
    {
        //
    }
}
