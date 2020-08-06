<?php

namespace App\Http\Controllers;

use App\Combo;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComboController extends Controller
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
    public function index($page = 0)
    {
        //
        $combos = Combo::all();
        return response()->json($combos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
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
        $user = auth("api")->user();
        $combo = new Combo($request->only(['combo', "name"]));
        $combo->user_id = $user->id;

        if ($combo->save()) {
            return response()->json(["message" => "Great, the combo has been added!"], 200);
        } else {
            return response()->json(["message" => "There was something wrong with your inputs!"], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Combo  $combo
     * @return \Illuminate\Http\Response
     */
    public function show(Combo $combo)
    {
        return response()->json($combo->with(['comments.replies'])->get(), 200);
    }

    private function checkAuthor($combo)
    {
        if ($combo->user->id !== auth("api")->user()->id && !auth("api")->user()->admin) {
            return response()->json(['message' => "You can't really edit this, so why are you trying?"], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Combo  $combo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Combo $combo)
    {
        //
        $this->checkAuthor($combo);
        $inputs = $request->all();
        $combo->update($inputs);
        if (!$combo->save()) {
            return response()->json(['message' => "Something went wrong"], 400);
        }
        return response()->json(['message' => "Hooray, hopefully now everything's gucci."], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Combo  $combo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Combo $combo)
    {
        $this->checkAuthor($combo);
        if (!$combo->delete()) {
            return response()->json(['message' => "Something went wrong."], 400);
        }

        return response()->json(['message' => "Well, okay. That's gone now."], 200);
    }

    public function comment(Combo $combo, Request $request)
    {
        $user = auth("api")->user();
        $comment = new Comment($request->only(['comment', 'rating']));
        $comment->user()->associate($user);
        $combo->comments()->save($comment);
        return response()->json([$combo->comments()->count()], 200);
    }
}
