<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Remise;

class RemiseControler extends Controller
{
    public function getOne(){
        return response()->json(Remise::first());
    }

    public function update(Request $request){
        try{
            $request->validate([
                'discount'=>'required|numeric|min:0|max:100'
            ]);
            $remise=Remise::first();
            if(!$remise){
                $remise=new Remise();
                $remise->setCreatedAt(date('Y-m-d H:i:s'));
            }
            else{
                $remise->updated_at=date('Y-m-d H:i:s');
            }
            $remise->discount=$request->discount;
            $remise->save();
            return response()->json([
                'message'=>'discount updated with success',
                'success'=>true
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'messsage'=>$e->getMessage(),
                'success'=>false
            ]);
        }
    }

}