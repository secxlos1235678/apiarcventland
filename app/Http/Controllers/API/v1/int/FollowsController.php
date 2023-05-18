<?php

namespace App\Http\Controllers\API\v1\int;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\FollowsResource;
use App\Http\Controllers\Controller;

class FollowsController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function store(User $user)
    {
		try{
        auth()->user()->following()->toggle($user->profile->id);
		return response([ 
			'message' => 'Successful'], 200);
		} catch (\Exception $e) {
			return response([ 'message' => $e->getMessage()], 201);
		}
    }
}
