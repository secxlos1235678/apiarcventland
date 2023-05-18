<?php

namespace App\Http\Controllers\API\v1\int;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostsResource;
use App\Http\Resources\SuggResource;

class PostsController extends Controller
{

    public function index()
    {
		try{
			// Array of users that the auth user follows
			$users_id = auth()->user()->following()->pluck('profiles.user_id');

			// Get Users Id form $following array
			$sugg_users = User::all()->reject(function ($user) {
				$users_id = auth()->user()->following()->pluck('profiles.user_id')->toArray();
				return $user->id == Auth::id() || in_array($user->id, $users_id);
			});

			// Add Auth user id to users id array
			$users_id = $users_id->push(auth()->user()->id);

			// $posts = Post::whereIn('user_id', $users)->with('user')->latest()->paginate(5);
			$posts = Post::whereIn('user_id', $users_id)->with('user')->latest()->paginate(10)->getCollection();

			// dd($posts);
			return response([ 'posts' => 
			PostsResource::collection($posts),
			'sugg_users' =>SuggResource::collection($sugg_users),	
			'message' => 'Successful'], 200);

			//return view('posts.index', compact('posts', 'sugg_users'));
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }

    public function explore()
    {
		try{
        $posts = Post::all()->except(Auth::id())->shuffle();
		return response([ 'posts' => 
        PostsResource::collection($posts),	
        'message' => 'Successful'], 200);
        //return view('posts.explore', compact('posts'));
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }

    public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
		try{
        $data = request()->validate([
            'caption' => ['required', 'string'],
            'image' => ['required', 'image']
        ]);

        $imagePath = request('image')->store('/uploads', 'public');

        $image = Image::make(public_path("storage/{$imagePath}"))->widen(600, function ($constraint) {
            $constraint->upsize();
        });
        $image->save();

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath
        ]);
		return response([ 
			
        'message' => 'Successful'], 200);

//        return response([ 'user' => auth()->user()]);
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }

    public function destroy(Post $post)
    {
		try{
        $this->authorize('delete', $post);

        $post->delete();
		return response([ 	
        'message' => 'Successful'], 200);
        //return Redirect::back();
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }

    public function show(Post $post)
    {
		try{
        $posts = $post->user->posts->except($post->id);
		return response([ 'posts' => 
        PostsResource::collection($posts),	
        'message' => 'Successful'], 200);
        //return view('posts.show', compact('post', 'posts'));
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }

    public function updatelikes(Request $request, $post)
    {
		try{
        // TODO Later
        $post = Post::where('id', $post)->first();
        if (!$post) {
            App::abort(404);
        }

        if ($request->update == "1") {
            // add 1 like
            $post->likes = $post->likes + 1;
            $post->save();
        } else if ($request->update == "0" && $post->likes != 0) {
            // take 1 like
            $post->likes = $post->likes - 1;
            $post->save();
        }

		return response([ 	
        'message' => 'Successful'], 200);
    //    return Redirect::to('/');
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }

    // methods for vue api requests
    public function vue_index()
    {
		try{
        $data = Post::orderBy('id')->with('user')->latest()->paginate(5);
        return response()->json($data);
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }
}
