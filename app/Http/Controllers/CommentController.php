<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
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
        $comment = new Comment($request->only(['parent_id', 'comment', 'rating']));
        $comment->user()->associate(auth("api")->user());
        if (!$comment->save()) {
            return response()->json(['message' => "Whoops, something went wrong!"], 400);
        }
        return response()->json(['message' => "Hooray, the comment is posted!"], 200);
    }

    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    private function checkAuthor($comment)
    {
        if ($comment->user_id !== auth("api")->user()->id) {
            return response()->json(['message' => "You can't really edit this, so why are you trying?"], 400);
        };
    }
    public function update(Comment $comment, Request $request)
    {
        $this->checkAuthor($comment);

        $comment->update($request->only(['comment', 'rating']));
        if ($comment->save()) {
            return response()->json(['message' => "Yay, you edited the comment!"], 200);
        }

        return response()->json(['message' => "Something went horribly wrong"], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
        $this->checkAuthor($comment);
        $comment->comment = "This comment was deleted";
        $comment->user_id = 0;
        if ($comment->save()) {
            return response()->json(['message' => "Yay, you deleted the comment!"], 200);
        }
    }
}
