<?php

namespace App\Http\Controllers;

use App\Character;
use App\Game;
use App\Property;
use Illuminate\Http\Request;

class ListController extends Controller
{
    //
    public function properties($game = null)
    {
        $games = $game ? Property::where(["game_id" => $game])->get() : Property::all();
        return response()->json($games, 200);
    }
    public function characters($game = null)
    {
        $games = $game ? Character::where(["game_id" => $game])->get() : Character::all();
        return response()->json($games, 200);
    }
    public function games()
    {
        return response()->json(Game::all(), 200);
    }
}
