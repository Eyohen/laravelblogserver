<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
// use Workbench\App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
// use Cloudinary\CloudinaryLaravel\Facades\Cloudinary;

class PostController extends Controller
{
    //
    public function create(Request $request){

        try{

            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            // $images = new ImageCrud()

        $request->validate([
            "title" => "required",
            "subheading" => "required",
            // "image" => "required | max:1024",
            "description" => "required | string"
            
        ]);


        // create post data
        $post = new Post();
        $post->user_id = auth()->user()->id;
        $post->title = $request->title;
        $post->subheading = $request->subheading;
        $post->description = $request->description;
        // $post->image = $request->image;
        $post->image = $imageName;

        // Save Image in storage folder
        Storage::disk('public')->put($imageName, file_get_contents($request->image));


        //save
        $post->save();

        //send response
        return response()->json([
            "status" => 1,
            "message" => "post created successfully"
        ]);

    }catch(\Exception $e){
        return response()->json([
            'message' => "Something went really wrong!!"
        ], 500);
    }

    }

    public function listpost(Request $request){



        // $posts = Post::get();
        $posts = Post::orderBy('created_at', 'desc')
        ->paginate(4);

       
        return response()->json([
            "status" => 1,
            "message" => "All posts successfully fetched",
            "data" => $posts->items(),
            "meta" => [
                "current_page" => $posts->currentPage(),
                "per_page" => $posts->perPage(),
                "total" => $posts->total(),
            ],
        ]);

    }

    public function authorpost(Request $request){
        $author_id = auth()->user()->id;
        // $posts = Author::with('post')->where('author', $author_id)->get();
        $posts = User::find($author_id)->posts;

        return response()->json([
            "status" => 1,
            "message" => "Author posts",
            "data" => $posts
        ]);
        

    }

    public function singlepost($post_id){
        // $user_id = auth()->user()->id;   

        if (Post::where([
            // "user_id" => $user_id,
            "id" => $post_id
        ])->exists()){

            $post = Post::find($post_id);

            return response()->json([
                "status"=> true,
                "message" => "post data found",
                "data" => $post
            ]);

        } else {

            return response()->json([
                "status"=> false,
                "message" => "post doesn't exist"
            ]);
        }

    }

    public function updatepost(Request $request, $post_id){
        $user_id = auth()->user()->id;

        if(Post::where([
            "user_id" => $user_id,
            "id" => $post_id
        ])->exists()){
            $post = Post::find($post_id);
            $post->title = isset($request->title) ? $request->title : $post->title;
            $post->subheading = isset($request->subheading) ? $request->subheading : $post->subheading;
            $post->description = isset($request->description) ? $request->description : $post->description;

            $post->save();
            return response()->json([
                "status" => 1,
                "message" => "post data has been updated"
            ]);

        } else {
             return response()->json([
                "status" => false,
                "message" => "post data not updated!!"
             ]);
        }
        

    }

    public function deletepost($post_id){
        $user_id = auth()->user()->id;
        
        if(Post::where([
            "user_id" => $user_id,
            "id" => $post_id
        ])->exists()){
            $post = Post::find($post_id);
            $post->delete();

            return response()->json([
                "status" => true,
                "message" => "post has been deleted"
            ]);

        } else {

            return response()->json([
                "status" => false,
                "message" => "User post doesnt exist"
            ]);
        }

    }

}
