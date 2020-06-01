<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessImage;
use App\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image as ImageIntervention;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Media::latest()
            ->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request = $request->validate(['file' => 'required']);

        // Upload original image
        $image_path = Storage::disk('public')->put('asd', $request['file']);
        $image_path_thumbnail = Storage::disk('public')->put('thumbnail', $request['file']);

        // Make thumbnail as well
        $thumbnail = ImageIntervention::make(public_path('storage/' . $image_path_thumbnail));
        if ($thumbnail->width() > 150) {
            $thumbnail->fit(150);
        }
        $thumbnail->save();
        
        $media = Media::create([
            'filename' => $image_path,
            'file_properties' => [
                'size' => $request['file']->getSize(),
                'original_name' => $request['file']->getClientOriginalName(),
                'mime_type' => $request['file']->getMimeType()
            ]
        ]);

        ProcessImage::dispatch($media->id, $image_path);

        return response()->json($media, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(media $media)
    {
        return response()->json($media, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);
        $media->update($request->all());

        return response()->json($media, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Media::find($id)->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  array  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_bulk(array $id)
    {
        Media::whereIn($id)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
