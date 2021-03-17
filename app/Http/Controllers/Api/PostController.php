<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->get();
        foreach ($posts as $post) {
            $post->user;
            $post['commentCount'] = count($post->comments);
            $post['likeCount'] = count($post->likes);
            $post['selfLike'] = false;
            foreach ($post->likes as $like) {
                if ($like->user_id == Auth::user()->id) {
                    $post['selfLike'] = true;
                }
            }
        }
        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }

    public function userPosts()
    {
        $userPosts = Auth::user()->posts;
        return response([
            'success'=>true,
            'posts'=>$userPosts
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $photo = '';
        // if($request->hasFile('photo')){
        //     $photo=$request->file('photo')->storePublicly('posts','public');
        // }
        if ($request->photo != '') {
            $photo = time() . '.jpg';
            file_put_contents('storage/posts' . $photo, base64_decode($request->photo));
            $photo = 'storage/posts' . $photo;
        }
        $post = Auth::user()->posts()->create([
            'text' => $request->text,
            'photo' => $photo
        ]);
        return response()->json([
            'success' => true,
            'message' => 'post created successfuly',
            'post' => $post
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (Auth::user()->id != $post->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'unauth change',
            ]);
        }

        // if()
        // $photo = '';
        // if ($request->hasFile('photo')) {
        //     Storage::disk('public')->delete($post->photo);
        //     $photo = $request->file('photo')->storePublicly('posts', 'public');
        // } else {
        //     $photo = $post->photo;
        // }
        $post->update([
            'text' => $request->text
        ]);
        return response()->json([
            'success' => true,
            'message' => 'post updated successfuly',
            'post' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if (Auth::user()->id != $post->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'unauth change',
            ]);
        }

        Storage::disk('public')->delete($post->photo);
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'post deleted successfuly'
        ]);
    }
}
