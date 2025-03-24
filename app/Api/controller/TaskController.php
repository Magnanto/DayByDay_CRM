<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskController extends Controller
{
    public function getAllTasks(){
        $tasks=Task::all();
        return response()->json($tasks);
    }

    public function getById($id){
        $task=Task::find($id);
        return response()->json($task);
    }
}