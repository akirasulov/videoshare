<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\ContentRangeUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileStoreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        throw new Exception();
        $receiver = new FileReceiver(
            UploadedFile::fake()->createWithContent('file', $request->getContent()),
            $request,
            ContentRangeUploadHandler::class
        );

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $this->storeFile($request, $save->getFile());
        }

        $save->handler();
    }

    protected function storeFile(Request $request, UploadedFile $file)
    {
        $now = Carbon::now()->format('Y-m-d');

        $request->user()->videos()->create([
            'title' => $now,
            'description' => 'A video captured on '.$now,
            'video_path' => $file->storeAs('videos', Str::uuid().'.mp4', 'public'),
        ]);

        return redirect()->route('videos.index');
    }
}
