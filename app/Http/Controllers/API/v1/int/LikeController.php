<?php

namespace App\Http\Controllers\API\v1\int;

use App\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Resources\LikeResource;
use App\Http\Controllers\Controller;

class LikeController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		try {
			$user = 3;
			$like = Like::where('user_id', $user)->where('post_id', $id)->get();
			return response([ 'message' => 'Successful'], 200);
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}
    }

    public function update2($id)
    {
		try{
			$user = Auth::User();

			$like = Like::where('user_id', $user->id)->where('post_id', $id)->first();

			if ($like) {
				$like->State = !$like->State;
				$like->save();
			} else {
				$like = Like::create([
					"user_id" => $user->id,
					"post_id" => $id,
					"State" => true

				]);
			}

			return response([ 'message' => 'Successful'], 200);
		   // return Redirect::to('/');
   		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}

    }

}
