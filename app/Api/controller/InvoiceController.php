<?php

namespace App\Api\controller;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;

class InvoiceController
{
    public function getAllInvoice(){
        $invoices=Invoice::all();
        return response()->json($invoices);
    }


    public function getById($id){
        $invoice=Invoice::find($id);
        return response()->json($invoice);
    }

    public function getInvoiceStatus(Request $request){
        $invoiceStatus=InvoiceStatus::fromStatus($request->status)->getDisplayValue();
        return response()->json($invoiceStatus);
    }
}