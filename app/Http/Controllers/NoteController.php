<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteCollection;
use App\Http\Resources\NoteResource;
use App\Http\Filters\NoteFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = new NoteFilter();
        $items = $filter->transform($request);
        if(count($items) == 0){
            return new NoteCollection(auth('sanctum')->user()->notes);
            //return new NoteCollection(Note::all());
        }
        else{
            return new NoteCollection(Note::where($items)->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNoteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNoteRequest $request)
    {
        $request->merge(['user_id' => auth('sanctum')->user()->id]);
        // save image
        if($request->exists('coverPhoto') && $request->get('coverPhoto') != null)
        {
            $this->saveImage($request);
        }
        return new NoteResource(Note::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        return new NoteResource($note);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNoteRequest  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $request->merge(['user_id' => auth('sanctum')->user()->id]);
        if($request->exists('coverPhoto') && $request->get('coverPhoto') != null )
        {
            if(isset($note->cover_photo)) Storage::disk('public')->delete('images/uploads/'.$note->cover_photo);
            $this->saveImage($request);
        }
        $note->update($request->all());
        return new NoteResource($note);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        if(isset($note->cover_photo)) Storage::disk('public')->delete('images/uploads/'.$note->cover_photo);
        $note->delete();
        return new NoteResource($note);
    }

    private function saveImage(Request $request){
            $data = $request->all();
            $image_64 = $request->get('coverPhoto');
            if (preg_match('/^data:image\/(\w+);base64,/', $image_64)) {
                $image_64 = substr($image_64, strpos($image_64, ','));
            }
            $image = base64_decode($image_64);
            $imgName = Str::random(10).".png";
            $saved = Storage::disk('public')->put('images/uploads/'.$imgName, $image);
            if($saved == 1){
                $data['coverPhoto'] = $imgName;
                $data['cover_photo'] = $imgName;
            }
            else{
                $data['coverPhoto'] == null;
            }
            $request->merge($data);
    }
}
