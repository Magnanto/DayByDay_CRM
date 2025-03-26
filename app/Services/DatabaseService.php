<?php

namespace App\Services;

use App\Enums\OfferStatus;
use App\Models\Absence;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\CreditLine;
use App\Models\Industry;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\UnavailableStream; // Add this import

class DatabaseService
{
    public function getTables()
    {
        $excluded_tables = ['business_hours','department_user','departments',
            'industries','migrations','permission_role',
            'permissions','role_user','roles','settings','statuses','users','clients','contacts'];

        // Obtenir toutes les tables
        $query = "SELECT table_name
                  FROM information_schema.tables
                  WHERE table_schema = 'daybyday'
                  AND table_name  NOT IN ('" . implode("','", $excluded_tables) . "')"; // Modify the query to exclude tables

        $tables = DB::select($query);

        return array_map(function($table) {
            return $table->table_name;
        }, $tables);
    }

    public function reset()
    {
        $tables = $this->getTables();

        try {
            // Désactiver les contraintes de clé étrangère
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }

            // Réactiver les contraintes de clé étrangère
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return true;
        } catch (\Exception $e) {
            // Log the exception if needed
            return false;
        }
    }


    public function import($projectFile,$taskFile, $leadFile)
    {
        $errors = [];

        try {
            $dataProject=$this->convertCSV($projectFile);
            $dataTask=$this->convertCSV($taskFile);
            $dataLead=$this->convertCSV($leadFile);
            DB::beginTransaction();

            foreach ($dataProject as $index => $row) {
                try {
                    $user = User::where('name', $row['client_name'])->first();
                    if (!$user) {
                        $user = factory(User::class)->create([
                            'name' => $row['client_name'],
                        ]);
                    }

                    $client = Client::where('user_id', $user->id)->first();
                    if (!$client) {
                        // Get a random industry
                        $industry = Industry::inRandomOrder()->first();
                        $industryId = $industry->id;

                        // Create a new client with the random industry
                        $client = factory(Client::class)->create([
                            'user_id' => $user->id,
                            'industry_id' => $industryId
                        ]);
                    }
                    //                $status= Status::inRandomOrder()->first();

                    $project = factory(Project::class)->create([
                        'client_id' => $client->id,
                        'status_id' => 12,
                        'user_assigned_id' => 1,
                        'user_created_id' => 1,
                        //                    'invoice_id'=>$invoice->id,
                        'title' => $row['project_title']
                    ]);

                }
                catch (\Exception $e) {
                    $errors[] = 'Échec de l\'importation (Project): ' . $e->getMessage() . ' at CSV line ' . ($index + 2);
                }
            }

            foreach ($dataTask as $index => $row) {
                try {
                    //                $status=Status::inRandomOrder()->first();

                    $project = Project::where('title', $row['project_title'])->first();
                    if (!$project) {
                        DB::rollBack();
                        return "Erreur: project '" . $row['project_title'] . "' inexistant || CSV line " . ($index + 2);
                    }

                    $client = Client::where('id', $project->client_id)->first();

                    $user = User::where('id', $client->user_id)->first();

                    $task = factory(Task::class)->create([
                        'title' => $row['task_title'],
                        'status_id' => 2,
                        'user_assigned_id' => 1,
                        'user_created_id' => 1,
                        'client_id' => $client->id,
                        'project_id' => $project->id,
                    ]);
                }
                catch (\Exception $e) {
                    $errors[] = 'Échec de l\'importation (Task): ' . $e->getMessage() . ' at CSV line ' . ($index + 2);
                }
            }
            foreach ($dataLead as $index => $row) {
                try{
                    if(empty(trim($row['client_name']))||
                        empty(trim($row['lead_title'])||
                            empty(trim($row['prix'])||
                                empty(trim($row['quantite']))||
                                    empty(trim($row['type']))||
                                        empty(trim($row['produit']))
                            ))){
                        $errors[] = "champ vide fichier Lead, ligne : " . ($index + 2);
                        continue;
                    }

                    if($row['prix'] <= 0){
                        $errors[] = "Montant incorrecte fichier Lead ligne : " . ($index + 2);
                        continue;
                    }

                    if($row['quantite']<=0 ||is_float($row['quantite'])){
                        $errors[] = "Quantite incorrecte fichier Lead ligne : " . ($index + 2);
                        continue;
                    }

                    $user= User::where('name',$row['client_name'])->first();
                    if (!$user) {
                        $user = factory(User::class)->create([
                            'name' => $row['client_name'],
                        ]);
                    }
                    $client = Client::where('user_id', $user->id)->first();
                    if (!$client) {
                        // Get a random industry
                        $industry = Industry::inRandomOrder()->first();
                        $industryId = $industry->id;

                        // Create a new client with the random industry
                        $client = factory(Client::class)->create([
                            'user_id' => $user->id,
                            'industry_id' => $industryId
                        ]);
                    }

                    $lead = Lead::where('title',$row['lead_title'])->first();
                    if(!$lead) {
                        $lead = factory(Lead::class)->create([
                            'title' => $row['lead_title'],
                            'status_id' => 7,
                            'user_assigned_id' => 1,
                            'client_id' => $client->id,
                            'user_created_id' => 1,
                        ]);
                    }

                    $produit=Product::where('name',$row['produit'])->first();
                    if(!$produit){
                        $produit=factory(Product::class)->create([
                            'name'=>$row['produit'],
                        ]);
                    }

                    if($row['type']=="offers"){
                        $offer=factory(Offer::class)->create([
                            'client_id'=>$client->id,
                            'source_id'=>$lead->id,
                        ]);
                        factory(InvoiceLine::class)->create([
                            "invoice_id"=>null,
                            "offer_id"=>$offer->id,
                            "type"=>$produit->default_type,
                            "quantity"=>$row['quantite'],
                            "price"=>$row['prix'],
                            "product_id"=>$produit->id
                        ]);
                    }
                    else{
                        $offer=factory(Offer::class)->create([
                            'client_id'=>$client->id,
                            'source_id'=>$lead->id,
                            "status"=>OfferStatus::won()->getStatus(),
                        ]);
                        factory(InvoiceLine::class)->create([
                            "invoice_id"=>null,
                            "offer_id"=>$offer->id,
                            "type"=>$produit->default_type,
                            "quantity"=>$row['quantite'],
                            "price"=>$row['prix'],
                            "product_id"=>$produit->id
                        ]);
                        $invoice = factory(Invoice::class)->create([
                            "client_id"=>$client->id,
                            "offer_id"=>$offer->id,
                            "source_type"=>Lead::class,
                            "source_id"=>$lead->id
                        ]);
                        factory(InvoiceLine::class)->create([
                            "invoice_id"=>$invoice->id,
                            "offer_id"=>null,
                            "type"=>$produit->default_type,
                            "quantity"=>$row['quantite'],
                            "price"=>$row['prix'],
                            "product_id"=>$produit->id
                        ]);
                    }
                }
                catch (\Exception $e) {
                    $errors[] = 'Échec de l\'importation (Lead): ' . $e->getMessage() . ' at CSV line ' . ($index + 2);
                }
            }
            if (!empty($errors)) {
                DB::rollBack();
                return $errors;
            }

            DB::commit();
            return 'Importation réussie';
        } catch (UnavailableStream $e) {
            DB::rollBack();
            return 'Échec de l\'importation: ' . $e->getMessage();
        } catch (\Exception $e) {
            DB::rollBack();
            return 'Échec de l\'importation: ' . $e->getMessage();
        }
    }

    public function convertCSV($filename){
        $delimiter = ',';
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }
//    public function checkForeignKey1($table_name){
//        $foreignKeyExists = DB::table('$table_name')->where('id', $record['foreign_key_id'])->exists();
//        return $foreignKeyExists;
//    }

//    public function checkForeignKeys($record)
//    {
//        // Get the foreign key constraints for the table
//        $foreignKeys = DB::select("
//        SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
//        FROM information_schema.KEY_COLUMN_USAGE
//        WHERE TABLE_SCHEMA = 'your_database_name'
//        AND TABLE_NAME = 'your_table_name'
//        AND REFERENCED_TABLE_NAME IS NOT NULL
//    ");

//        foreach ($foreignKeys as $foreignKey) {
//            $columnName = $foreignKey->COLUMN_NAME;
//            $referencedTable = $foreignKey->REFERENCED_TABLE_NAME;
//            $referencedColumn = $foreignKey->REFERENCED_COLUMN_NAME;
//
//            // Check if the foreign key value exists in the referenced table
//            $exists = DB::table($referencedTable)
//                ->where($referencedColumn, $record[$columnName])
//                ->exists();
//
//            if (!$exists) {
//                return false;
//            }
//        }
//
//        return true;
//    }

}

