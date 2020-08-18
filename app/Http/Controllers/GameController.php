<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GameController extends Controller
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
    public function index($game = null)
    {
        //
        $characters = Game::all();

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
    public function show(Game $game)
    {
        //
        return response()->json($game, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
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
            if (!empty($game->image)) {
                Storage::delete($game->image);
            }
            $randomString = Str::random(4);
            $image =  Storage::putFileAs("/public/uploads/images", $request->file("image"), "{$game->slug}-image-{$randomString}.{$request->file("image")->getClientOriginalExtension()}");
            $game->image = str_replace("public", "storage", $image);
        }
        $game->name = $inputs['name'];
        $game->description = $inputs['description'];
        if ($game->save()) {
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
