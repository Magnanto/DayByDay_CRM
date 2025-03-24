<?php

namespace App\Api\controller;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\Offer;

class OfferController extends Controller
{
    public function getAllOffers(){
        $offers = Offer::all();
        return response()->json($offers);
    }

    public function getById($id){
        $offer=Offer::find($id);
        return response()->json($offer);
    }

    public function countOffer(){
//        $count=Offer::count();
        return response()->json(['count'=>12],404);
    }

    public function getInProgess(){
        $offers=Offer::where('status',OfferStatus::inProgress())->get();
        return $offers;
    }

    public function getWon(){
        $offers=Offer::where('status',OfferStatus::won())->get();
        return $offers;
    }

    public function getLost(){
        $offers=Offer::where('status',OfferStatus::lost())->get();
        return $offers;
    }

}