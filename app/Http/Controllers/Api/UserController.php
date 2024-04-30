<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;


class UserController extends Controller
{
    //
    public function register(Request $request){
        $request->validate([
            "name"=>"required",
            "email"=>"required | email | unique:users",
            "password"=> "required | confirmed",
          
        ]);

        // save to database
        DB::table('users')->insert([
            'name'=> $request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);

        //json response

        return response([
            'status' => 1,
            'message' => "User Created"
        ]);


}

public function login(Request $request){
    $login_data = $request->validate([
        "email"=>"required",
        "password"=> "required",
       
    ]);

    // validate author data
    if(!auth()->attempt($login_data)){

        return response()->json([
            "status" => false,
            "message" => "invalid Credentials"
        ]);
    }

    // token
    $token  = auth()->user()->createToken("auth_token")->accessToken;

    // send response
    return response()->json([
        "status"=> true,
        "message"=> "User Logged in successfully",
        "access_token"=> $token
    ]);

}



public function profile(Request $request){
    $user_data = auth()->user();

    return response()->json([
        "status" => true,
        "message"=>"User Data",
        "data" => $user_data
    ]);
    

}


public function logout(Request $request){
    $token = $request->user()->token();

    $token->revoke();

    return response()->json([
        "status" => true,
        "message" => "Logout Successfully"
    ]);
}

//function to refetch user
public function refetch(Request $request)
{
    // Retrieve the authenticated user
    // $user = $request->user();
    $user = auth()->user();

    // Check if user is authenticated
    if ($user) {
        // Return user data in JSON response
        return response()->json([
            'status' => true,
            'message' => 'User details fetched successfully',
            'user' => $user,
        ]);
    } else {
        // If user is not authenticated, return error response
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }
}


public function singleUser($user_id){
    $user_id = auth()->user()->id;   

    if (User::where([
        "user_id" => $user_id,
        // "id" => $post_id
    ])->exists()){

        $user = User::find($user_id);

        return response()->json([
            "status"=> true,
            "message" => "post data found",
            "data" => $user
        ]);
    } else {
        return response()->json([
            "status"=> false,
            "message" => "post doesn't exist"
        ]);
    }
}

}

