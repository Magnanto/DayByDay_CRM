<?php

namespace App\Api\controller;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Services\Invoice\InvoiceCalculator;
use Illuminate\Http\Request;

class InvoiceController
{
    public function getAllInvoice(){
        $invoices=Invoice::all();
        return response()->json($invoices);
    }


    public function getById($id){
        $invoice=Invoice::find($id);
        if(!$invoice){
            return response()->json(['message'=>'Aucun invoice pour l id '.$id],404);
        }
        return response()->json($invoice);
    }

    public function getInvoiceStatus(Request $request){
        $invoiceStatus=InvoiceStatus::fromStatus($request->status)->getDisplayValue();
        return response()->json($invoiceStatus);
    }

    public function getMontantInvoiceMensuelle(){
        try {
            $currentYear = date('Y');
            $monthlyData = [];

            for ($month = 1; $month <= 12; $month++) {
                $invoices = Invoice::whereMonth('created_at', $month)
                    ->whereYear('created_at', $currentYear)
                    ->get();

                $totalWithoutTax = 0;
                $totalWithTax = 0;

                foreach ($invoices as $invoice) {
                    $invoiceCalculator = new InvoiceCalculator($invoice);
                    $totalWithTax += $invoiceCalculator->getSubTotal()->getAmount();
                    $totalWithoutTax += $invoiceCalculator->getTotalPrice()->getAmount();
                }

                $monthlyData[] = [
                    'month' => $month,
                    'amountWithoutTax' => $totalWithoutTax,
                    'amountWithTax' => $totalWithTax,
                ];
            }

            return response()->json($monthlyData);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
    }

    
}