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
            return new NoteCollection(Note::all());
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
        $data = $request->all();
        $image_64 = $request->get('coverPhoto');
        if (preg_match('/^data:image\/(\w+);base64,/', $image_64)) {
            $image_64 = substr($image_64, strpos($image_64, ',') + 1);
        }
        $image = base64_decode($image_64);
        $imgName = Str::random(10).".png";
        $saved = Storage::disk('public')->put('images/uploads/'.$imgName, $image);
        if($saved == 1){
            $data['coverPhoto'] = $imgName;
            $data['cover_photo'] = $imgName;
        }
        $request->merge($data);
        return new NoteResource(Note::create($data));
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
        $data = $request->all();
        $image_64 = $request->get('coverPhoto');
        if (preg_match('/^data:image\/(\w+);base64,/', $image_64)) {
            $image_64 = substr($image_64, strpos($image_64, ',') + 1);
        }
        $image = base64_decode($image_64);
        $imgName = Str::random(10).".png";
        $saved = Storage::disk('public')->put($imgName, $image);
        if($saved == 1){
            $data['coverPhoto'] = $imgName;
            $data['cover_photo'] = $imgName;
        }
        $request->merge($data);
        if($note->cover_photo != null) Storage::disk('public')->delete($note->cover_photo);
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
        $note->delete();
        return new NoteResource($note);
    }
}
