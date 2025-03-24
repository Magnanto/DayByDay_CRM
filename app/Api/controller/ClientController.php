<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    public function getAllClients(){
        $clients=Client::all();
        return response()->json($clients);
    }

    public function getClientById($id){
        $client=Client::find($id);
        return response()->json($client);
    }

    public function total(){
        return response()->json(Client::all());
    }
    public function countClient(){
        $count = Client::all()->count();
        return response()->json(['count' => 'test']);
    }

}
