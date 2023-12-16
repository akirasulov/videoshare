<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class GenerateVideoPreviewImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Video $video)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        FFMpeg::fromDisk('public') //grab a video
            ->open($this->video->video_path) //grab a still from video
            ->getFrameFromSeconds(0) //get frame
            ->export() //export frame
            ->toDisk('public') //save still in public
            ->afterSaving(function ($exporter, Media $media) {
                $this->video->update([
                    'still_path' => $media->getPath(),
                ]);
            })
            ->save('stills/' . Str::uuid() . '.jpg'); //update video still path

    }
}
