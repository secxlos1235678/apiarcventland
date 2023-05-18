<?php

namespace App\Http\Controllers\API\v1\int;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ProfilesResource;
use App\Http\Controllers\Controller;


class ProfilesController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index(User $user)
    {
		try{
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->profile) : false;
        $postCount = Cache::remember(
            'count.posts.' . $user->id,
            now()->addSeconds(10),
            function () use ($user) {
                return $user->posts->count();
            }
        );

        $followersCount = Cache::remember(
            'count.followers.' . $user->id,
            now()->addSeconds(10),
            function () use ($user) {
                return $user->profile->followers->count();
            }
        );

        $followingCount = Cache::remember(
            'count.following.' . $user->id,
            now()->addSeconds(10),
            function () use ($user) {
                return $user->following->count();
            }
        );
		
			return response([ 'follows' => $follows,
			'user' =>$user,
			'postCount' =>$postCount,	
			'followersCount' =>$followersCount,	
			'followingCount' =>$followingCount,	
			'message' => 'Successful'], 200);
        //return view('profiles.index', compact('user', 'follows', 'postCount', 'followersCount', 'followingCount'));
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user->profile);
		return response([ 'message' => 'Successful'], 200);
//        return view('profiles.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
		try{
        $this->authorize('update', $user->profile);

        $dataProfile = $request->validate([
            'website' => ['sometimes', 'url', 'nullable'],
            'bio' => ['sometimes', 'string', 'nullable'],
            'image' => ['sometimes', 'image', 'max:3000']
        ]);

        $dataUser = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        if (request('image')) {
            $imagePath = request('image')->store('/profile', 'public');
            $image = Image::make(public_path("storage/{$imagePath}"))->fit(300, 300);
            $image->save();
            $imageArray = ['image' => $imagePath];
        }

        auth()->user()->profile->update(array_merge(
            $dataProfile,
            $imageArray ?? []
            // ['image' => $imagePath ?? $user->profile->image]
        ));

        auth()->user()->update($dataUser);
		return response([ 
			'username' =>auth()->user()->username,	
			'message' => 'Successful'], 200);
        return redirect('/profile/' . auth()->user()->username);
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}
    }

    public function search(Request $request)
    {
		try{
        $q = $request->input('q');
        $user = User::where('username', 'LIKE', '%' . $q . '%')->orWhere('email', 'LIKE', '%' . $q . '%')->get();
        if (count($user) > 0){
			return response([ 'follows' => 
			PostsResource::collection($follows),
			'sugg_users' =>$user,
			'message' => 'Successful'], 200);
		}else{
			return response([ 
			'username' =>auth()->user()->username,	
			'message' => 'No results found.'], 201);
		}
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}
//            return view('profiles.search')->withDetails($user)->withQuery($q);
//        return view('profiles.search')->withMessage('No results found.');
    }
}
