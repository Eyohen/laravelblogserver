<?php

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("register", [UserController::class, 'register']);
Route::post("login", [UserController::class, 'login']);
Route::get("list-posts", [PostController::class,"listpost"]);
Route::get("single-post/{id}", [PostController::class,"singlepost"]);




Route::group(["middleware" => ["auth:api"]], function(){
    Route::get("profile", [UserController::class,"profile"]);
    Route::get("refetch", [UserController::class,"refetch"]);
    Route::get("user/{id}", [UserController::class,"singleUser"]);


    Route::post("logout", [UserController::class,"logout"]);

    Route::post("create-post", [PostController::class,"create"]);
  
    Route::post("author-posts", [PostController::class,"authorpost"]);
   

    Route::put("update-post/{id}", [PostController::class,"updatepost"]);
    Route::delete("delete-post/{id}", [PostController::class,"deletepost"]);


});