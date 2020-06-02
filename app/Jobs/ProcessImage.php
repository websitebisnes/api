<?php

namespace App\Jobs;

use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ImageIntervention;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $media_id;
    protected $image_path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $media_id, string $image_path)
    {
        $this->media_id = $media_id;
        $this->image_path = $image_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get original image and compress
        $image = ImageIntervention::make(public_path('storage/' . $this->image_path));
        if ($image->width() > 1024) {
            $image->fit(1024);
        }
        $image_stream = $image->stream('jpg', 85);

        // Make thumbnail
        if ($image->width() > 150) {
            $image->fit(150);
        }

        $thumbnail_stream = $image->stream('jpg', 100);

        $s3_upload = Storage::disk('s3')->put($this->image_path, $image_stream, 'public');
        Storage::disk('s3')->put('thumbnail/' . $this->image_path, $thumbnail_stream, 'public');

        // And update image cloud_url
        if ($s3_upload) {
            // Delete original image
            Storage::disk('public')->delete($this->image_path);
            Media::find($this->media_id)->update(['is_cloud' => 1]);
        }
    }
}
