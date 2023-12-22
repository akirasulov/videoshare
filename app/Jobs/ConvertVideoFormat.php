<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertVideoFormat implements ShouldQueue
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
            ->export() //export frame
            ->inFormat(new \FFMpeg\Format\Video\X264())
            ->toDisk('public') //save still in public
            ->afterSaving(function ($exporter, Media $media) {
                //delete original video
                Storage::disk('public')->delete($this->video->video_path);

                $this->video->update([
                    'video_path' => $media->getPath(),
                ]);
            })
            ->save('videos/'.Str::uuid().'.mp4'); //update video still path
    }
}
