<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function getAllPayment(){
        $payments=Payment::all();
        return response()->json($payments);
    }

    public function getById($id){
        $payment=Payment::find($id);
        return response()->json($payment);
    }
}