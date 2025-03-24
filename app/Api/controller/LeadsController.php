<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Lead;

class LeadsController extends Controller
{
    public function getAllThisMonth(){
        $lead=new Lead();
        $leads=$lead->getThisMonth();
        return response()->json($leads);
    }
}