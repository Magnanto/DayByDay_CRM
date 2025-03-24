<?php

namespace App\Api\controller;

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
}