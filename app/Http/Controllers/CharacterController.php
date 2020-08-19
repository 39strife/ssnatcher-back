<?php

namespace App\Http\Controllers;

use App\Character;
use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CharacterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $characters = [];
        if ($request->game) {
            $characters = Game::first('slug', $request->game)->first()->load("characters")['characters'];
        } else {
            $characters = Character::all();
        }
        //


        return response()->json($characters, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function show(Character $character)
    {
        //
        return response()->json($character, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Character $character)
    {
        $validationRules = [
            'image' => "required|image|max:5120",
            'name' => "required|max:256",
            'description' => 'required|max:5120',
        ];
        $inputs = $request->only(['image', 'name', 'description']);
        $validator = Validator::make($inputs, $validationRules);
        if ($validator->fails()) {
            return response()->json(['message' => "There was some kind of problem here.", 'errors' => $validator->errors()], 400);
        }
        if ($request->hasFile('image')) {
            if (!empty($character->image)) {
                Storage::delete($character->image);
            }
            $randomString = Str::random(4);
            $image =  Storage::putFileAs("/public/uploads/images", $request->file("image"), "{$character->slug}-image-{$randomString}.{$request->file("image")->getClientOriginalExtension()}");
            $character->image = str_replace("public", "storage", $image);
        }
        $character->name = $inputs['name'];
        $character->description = $inputs['description'];
        if ($character->save()) {
            return response()->json(['message' => "Succesfully saved!"], 200);
        }

        return response()->json(['message' => "Something went wrong!"], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function destroy(Character $character)
    {
        //
    }
}
