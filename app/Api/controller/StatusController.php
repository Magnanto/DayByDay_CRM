<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Status;

class StatusController extends Controller
{
    public function getById($id){
        $status= Status::find($id)->first;
        return response()->json($status);
    }
}