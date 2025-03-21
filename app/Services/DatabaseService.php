<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\CreditLine;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class DatabaseService
{
    public function getTables()
    {
        $excluded_tables = ['tasks'];

        // Obtenir toutes les tables
        $query = "SELECT table_name
                  FROM information_schema.tables
                  WHERE table_schema = 'daybyday'
                  AND table_name IN ('" . implode("','", $excluded_tables) . "')";

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

    public function import($file,$table_name)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $separateur = ",";
        $records = $csv->getRecords();
        $columns=explode($separateur,$records[0]);

        $csv->setHeaderOffset(0); // Assuming the first row contains the header

        DB::beginTransaction();

        try {
            foreach ($records as $record) {
                // Check foreign key constraints
                if ($this->checkForeignKeys($record)) {

                    // Insert each record into the database
                    $data = [];
                    foreach ($columns as $column) {
                        $data[$column] = $record[$column];
                    }

                    DB::table($table_name)->insert($data);


                } else {
                    // If foreign key constraint is not met, rollback and throw an exception
                    DB::rollBack();
                    throw new \Exception('Foreign key constraint not met for record: ' . json_encode($record));
                }
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            // Log the error or handle it as needed
            throw $e;
        }
    }



    public function checkForeignKey($table_name){
        $foreignKeyExists = DB::table('$table_name')->where('id', $record['foreign_key_id'])->exists();
        return $foreignKeyExists;
    }

}
