<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Invoice\GenerateInvoiceStatus;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function getAllPayment(){
        $payments=Payment::all();
        return response()->json($payments);
    }

    public function getPaymentInvoice($id){
        $payment=Payment::find($id);
        $invoice=$payment->invoice()->first();
        return $invoice;
    }

    public function getById($id){
        $payment=Payment::find($id);
        return response()->json($payment);
    }

    public function update(Request $request,$id){
        try{
            $payment=Payment::find($id);
            if(!$payment){
                return response()->json([
                    'message' => 'Payment not found',
                    'succes'=>false
                ]);
            }
            $payment->amount=$request->amount;
            $payment->save();

            $invoice=$payment->invoice()->first();
            app(GenerateInvoiceStatus::class,['invoice'=>$invoice])->createStatus();

            return response()->json([
                'message'=>'Payment updated successfully',
                'success'=>true,
            ]);
        }
        catch (\Exception $e){
            return response()->json([
                'message'=>$e->getMessage(),
                'success'=>false
            ]);
        }
    }

    public function delete($id){
        $payment=Payment::find($id);
        if($payment){
            $invoice=$payment->invoice()->first();
            app(GenerateInvoiceStatus::class,['invoice'=>$invoice])->createStatus();
            $payment->delete();
            return response()->json([
                'success'=>true,
                'message'=>'Payment successfully deleted'
            ]);
        }
        else{
            return response()->json([
                'sucess'=>'false',
                'message'=>'Payment not found'
            ]);
        }

    }
//    public get
}