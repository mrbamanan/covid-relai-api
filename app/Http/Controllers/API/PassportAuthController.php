<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class PassportAuthController extends Controller
{
    /**
     * PassportAuthController constructor.
     */
    public function __construct()
    {
     //   $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
 public function register(Request $request){
    $data = $request->validate([
        "name" => "required|string",
        "email"=>"required|email|unique:users",
        "password"=>"required|string|min:8",
        "password_confirm"=>"required|same:password"
    ]);

    $user = User::create([
       "name"=>$data['name'],
       "email"=>$data['email'],
       "password"=>bcrypt($data['password'])
    ]);


    $token = $user->createToken('covidrelai')->accessToken;

     return response()->json(['token' => $token], 200);
 }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if (!auth()->attempt($data)) {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
            $token = auth()->user()->createToken('covidrelai')->accessToken;
            return response()->json(['token' => $token], 200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(){
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

}
