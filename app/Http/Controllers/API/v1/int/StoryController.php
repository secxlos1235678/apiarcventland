<?php

namespace App\Http\Controllers\API\v1\int;

use App\Story;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Http\Resources\StoriesResource;
use App\Http\Controllers\Controller;

class StoryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		try{
        $data = request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $imagePath = request('image')->store('/story', 'public');

        $image = Image::make(public_path("storage/{$imagePath}"));
        $image->resize(500, 751);
        $image->save();

        auth()->user()->stories()->create([
            'image' => "http://localhost:8000/storage/" . $imagePath
        ]);
		return response([ 
			'username' =>auth()->user()->username,
			'message' => 'Successful'], 200);
//        return redirect('/profile/' . auth()->user()->username);
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
		try{
        $stories = $user->stories;
		return response([ 
			'user' =>$user,
			'stories' =>StoriesResource::collection($stories),
			'message' => 'Successful'], 200);
       // return view('stories.show', compact('stories', 'user'));
	   } catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}
		
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function edit(Story $story)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Story $story)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function destroy(Story $story)
    {
        //
    }
}
