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
use League\Csv\Reader;
use League\Csv\UnavailableStream; // Add this import

class DatabaseService
{
    public function getTables()
    {
        $excluded_tables = ['tasks'];

        // Obtenir toutes les tables
        $query = "SELECT table_name
                  FROM information_schema.tables
                  WHERE table_schema = 'daybyday'
                  AND table_name NOT IN ('" . implode("','", $excluded_tables) . "')"; // Modify the query to exclude tables

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

    public function import($file, $table_name)
    {
        try {
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0); // Assuming the first row contains the header

            $records = $csv->getRecords();
            DB::beginTransaction();

            foreach ($records as $record) {
                DB::table($table_name)->insert($record);
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

    public function checkForeignKey1($table_name){
        $foreignKeyExists = DB::table('$table_name')->where('id', $record['foreign_key_id'])->exists();
        return $foreignKeyExists;
    }

    public function checkForeignKeys($record)
    {
        // Get the foreign key constraints for the table
        $foreignKeys = DB::select("
        SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = 'your_database_name'
        AND TABLE_NAME = 'your_table_name'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");

        foreach ($foreignKeys as $foreignKey) {
            $columnName = $foreignKey->COLUMN_NAME;
            $referencedTable = $foreignKey->REFERENCED_TABLE_NAME;
            $referencedColumn = $foreignKey->REFERENCED_COLUMN_NAME;

            // Check if the foreign key value exists in the referenced table
            $exists = DB::table($referencedTable)
                ->where($referencedColumn, $record[$columnName])
                ->exists();

            if (!$exists) {
                return false;
            }
        }

        return true;
    }

}

