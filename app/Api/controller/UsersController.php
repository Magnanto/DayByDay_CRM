<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;

class UsersController extends Controller
{
    public function getAllUsers(){
        $users=User::all();
        return response()->json($users);
    }

    public function getByMail($mail){
        $user = User::where('email', $mail)->first();
        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function getById($id){
        $user=User::find($id);
        return response()->json($user);
    }
}
