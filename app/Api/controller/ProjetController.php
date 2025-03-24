<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjetController extends Controller
{
    public function getAllProjets(){
        $projets=Project::all();
        return response()->json($projets);
    }

    public function getById($id){
        $projet=Project::where('external_id',$id)->first();
        if($projet){
            return response()->json($projet);
        }
        else{
            return response()->json(['message' => 'Projet not found'], 404);
        }
    }

    public function count(){
        $projets=Project::all();
        if($projets->isNotEmpty()){
            $count=$projets->count();
            return response()->json($count);
        }
        else{
            $count=0;
            return response()->json($count);
        }
    }

}