<?php

namespace App\Api\controller;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function getTotals()
    {
        // Récupérer les données de la base de données
        $data = [
            'totalClients' => Client::count(),
            'totalProjects' => Project::count(),
            'totalTasks' => Task::count(),
            'totalOffers' => Offer::count(),
            'totalInvoices' => Invoice::count(),
            'totalPayments' => Payment::sum('amount'),
        ];

        // Retourner les données au format JSON
        return response()->json($data);
    }
}