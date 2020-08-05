<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Create a new user.
     *
     * @bodyParam  email
     * @bodyParam  password
     * @bodyParam  password_confirmation
     */
    public function register(Request $request)
    {
        $rules = [
            'email' => 'required|unique:users|max:255',
            'password' => 'required|confirmed',
        ];
        $response = ['message' => "Something went wrong", 'success' => false];
        $inputs = $request->only(['email', 'password', 'password_confirmation']);
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $response['errors'] = $validator->errors();
        } else {
            $user = new User($inputs);
            $user->password = Hash::make($inputs['password']);
            $user->email_verification = Str::random(60);
            $user->save();
            $response = ['message' => "Great! Check your email", 'success' => true];
        }

        return response()->json($response);
    }
    /**
     * Authenticate email.
     *
     * @bodyParam  token
     */
    public function authenticate(Request $request)
    {
        $token = $request->input("token");
        if (empty($token)) {
            return response()->json(['message' => "So no token?"], 400);
        }
        $user = User::firstWhere(["email_verification" => $token]);
        if ($user) {
            $user->email_verification = NULL;
            $user->markEmailAsVerified();
            $user->save();
            return response()->json(['message' => "Great! You can log in now!"]);
        }
        return response()->json(['message' => "Oh dang, there seems to be an error here?"], 400);
    }
    /**
     * Resend verification email.
     *
     * @bodyParam  email
     */
    public function resend(Request $request)
    {
        $rules = ['email' => 'required|exists:users|max:255'];
        $inputs = $request->only(['email']);
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()]);
        }
        $user = User::firstWhere($inputs);
        return $user;
        if (empty($user)) {
            return response()->json(["message" => "User does not exist. Try signing up?"], 400);
        }
        return response()->json([
            "message" =>
            $user->hasVerifiedEmail() ?
                "Looks like you've already verified, bud."
                :
                "Great, if you haven't verified your email yet, you should get a new email now!"
        ],             $user->hasVerifiedEmail() ?
            400
            : 200);
    }
    /**
     * Login.
     *
     * @bodyParam  email
     * @bodyParam  password
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => "required|min:8"
        ];
        $validator = Validator::make($request->all(), $rules);
        $credentials = request(['email', 'password']);
        if ($validator->fails()) {
            return response()->json(['message' => "Something's not correct here!", "errors" => $validator->errors()], 400);
        }
        if (!User::where("email", '=', $credentials['email'])->first()->hasVerifiedEmail()) {
            return response()->json(['message' => "You gotta verify your email!"], 400);
        }
        if (!($token = auth("api")->attempt($credentials))) {
            return response()->json(['message' => "Something's not correct here!"], 401);
        }
        return $this->respondWithToken($token);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth("api")->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
