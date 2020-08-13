<?php

namespace App\Http\Controllers;

use App\Mail\UserVerification;
use App\Profile;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'email' => 'required|email|unique:users|max:255|min:8',
            'username' => 'required|unique:users|max:255|min:8|alpha_dash',
            'password' => 'required|confirmed|min:8',
        ];
        $response = ['message' => "Something went wrong", 'success' => false];
        $inputs = $request->only(['email', 'password', 'password_confirmation', 'username']);
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $response['errors'] = $validator->errors();
        } else {
            $user = new User($inputs);
            $user->username = $inputs['username'];
            $user->password = Hash::make($inputs['password']);
            $user->email_verification = Str::random(60);
            $user->save();
            Mail::to($user->email)->send(new UserVerification($user->email_verification));
            $response = ['message' => "Great! Check your email", 'success' => true];
        }

        return response()->json($response, $response['success'] ? 200 : 400);
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
        $credentials = $request->only(['username', 'email', 'password']);
        $validator = Validator::make($credentials, [
            'username' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Something's not correct here!", "errors" => $validator->errors()], 400);
        }
        $user = User::where(array_key_exists('username', $credentials) ? "username" : "email", '=', array_key_exists('username', $credentials) ? $credentials['username'] :  $credentials['email'])->first();
        if ($user && !$user->hasVerifiedEmail()) {
            return response()->json(['message' => "You gotta verify your email!"], 400);
        }
        if (!$user) {
            return response()->json(['message' => "There's no such user!"], 400);
        }
        if (!($token = auth("api")->attempt($credentials, ['exp' => Carbon::now()->addDays(7)->timestamp]))) {
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
            'expires_in' => auth('api')->factory()->getTTL() * env('JWT_TTL', 10080)
        ]);
    }

    public function me()
    {
        $user = User::find(auth("api")->id());
        return response()->json($user->load('profile'), 200);
    }

    public function profile(User $user)
    {
        if (!$user) {
            return response()->json(['message' => "We can't find that profile"], 400);
        }
        return response()->json($user->load(['profile', 'combos', 'posts']), 200);
    }
}
