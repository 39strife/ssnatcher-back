<?php

namespace App\Http\Controllers;

use App\Character;
use App\Combo;
use App\ComboProperties;
use App\Comment;
use App\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
        $combos = Combo::with(["ratings"])->paginate(15, ['*'], "page", $page);
        foreach ($combos as $i => $value) {
            $combos[$i] = $value->avarageRating()->hasUserRated();
        }
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

    protected function makeImage($image)
    {
        $path = "";
        if ($image) {
            $randomString = Str::random(6);
            $avatar =  Storage::putFileAs("/public/uploads/characterUploads", $image, "{$randomString}.{$image->getClientOriginalExtension()}");
            $path = str_replace("public", "storage", $avatar);
        }
        return $path;
    }
    public function store(Request $request)
    {
        //        
        $user = auth("api")->user();
        $inputs = $request->only([
            "combo",
            "name",
            "image",
            "game",
            "character",
            "content",
        ]);
        $validator = Validator::make($inputs, [
            'combo' => 'required',
            'name' => 'required|max:256',
            'image' => 'image|max:1024',
            'game' => 'required',
            'character' => 'required',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => "Something went wrong!", "errors" => $validator->errors()], 400);
        }
        $character = Character::where('slug', $inputs['character'])->first();
        $image = $character->image;
        if ($request->hasFile("image")) {
            $image = $this->makeImage($request->file("image"));
        }
        $combo = new Combo;
        $combo->image = $image;
        $combo->name = $inputs['name'];
        $combo->combo = $inputs['combo'];
        $combo->description = $inputs['content'];
        $combo->character()->associate($character);
        $combo->user()->associate($user);

        if ($combo->save()) {
            foreach ($request->input("property") as $key => $value) {
                if (!empty($value)) {
                    $comboProperty = new ComboProperties;
                    $comboProperty->combo_id = $combo->id;
                    $comboProperty->property_id = $key;
                    $comboProperty->value = $value;
                    $comboProperty->save();
                }
            }
            return response()->json(["message" => "Great, the combo has been added!", "combo" => $combo], 200);
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
        return response()->json($combo->load(['comments.replies.replies.replies.replies.replies', 'ratings'])->hasUserRated()->avarageRating(), 200);
    }

    private function checkAuthor($combo)
    {
        if ($combo->user->id !== auth("api")->user()->id && !auth("api")->user()->role > 3) {
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Combo  $combo
     * @return \Illuminate\Http\Response
     */

    public function comment(Combo $combo, Request $request)
    {
        error_log(json_encode($request->all()));
        $return = $combo->comment($request->input('comment'));
        return response()->json($return[0], $return[1]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Combo  $combo
     * @return \Illuminate\Http\Response
     */
    public function rate(Combo $combo, Request $request)
    {
        $rating = $combo->rate($request->input("rating"));
        return response()->json($rating[0], $rating[1]);
    }
}
