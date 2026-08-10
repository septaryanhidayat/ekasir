<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    public function download()
    {
        $tables = [
            'tenants',
            'users',
            'suppliers',
            'products',
            'expenses',
            'cash_registers',
            'cash_flows',
            'transactions',
            'transaction_details',
            'migrations',
        ];

        $output = "-- ==============================================\n";
        $output .= "-- E-KASIR POS DATABASE BACKUP\n";
        $output .= "-- Exported Date: " . now('Asia/Jakarta')->format('Y-m-d H:i:s') . " WIB\n";
        $output .= "-- Exported By: " . (auth()->user()->name ?? 'System') . "\n";
        $output .= "-- ==============================================\n\n";
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $output .= "PRAGMA foreign_keys = OFF;\n\n";
        } else {
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        }

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)->get();

            $output .= "-- ----------------------------------------------\n";
            $output .= "-- Table structure & data for `$table` (" . count($rows) . " rows)\n";
            $output .= "-- ----------------------------------------------\n";
            $output .= "DELETE FROM `$table`;\n";

            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $keys = array_keys($rowArray);
                $fields = implode('`, `', $keys);

                $values = array_map(function ($val) {
                    if (is_null($val)) return 'NULL';
                    if (is_bool($val)) return $val ? '1' : '0';
                    if (is_numeric($val) && !is_string($val)) return $val;
                    return DB::connection()->getPdo()->quote((string) $val);
                }, array_values($rowArray));

                $valStr = implode(', ', $values);
                $output .= "INSERT INTO `$table` (`$fields`) VALUES ($valStr);\n";
            }

            $output .= "\n";
        }

        if ($driver === 'sqlite') {
            $output .= "PRAGMA foreign_keys = ON;\n";
        } else {
            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        }

        $filename = 'ekasir_backup_' . date('Ymd_His') . '.sql';

        return response($output, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:20480',
        ]);

        $file = $request->file('backup_file');
        $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));

        if (!in_array($extension, ['sql', 'txt'])) {
            return back()->with('error', 'Gagal meng-import: Format file harus berupa file .sql atau .txt.');
        }

        $sqlContent = file_get_contents($file->getRealPath());

        if (empty(trim($sqlContent))) {
            return back()->with('error', 'Gagal meng-import: File backup SQL kosong.');
        }

        $driver = DB::getDriverName();

        // Strip foreign key check statements incompatible with current driver
        if ($driver === 'sqlite') {
            $sqlContent = preg_replace('/SET\s+FOREIGN_KEY_CHECKS\s*=\s*[01]\s*;/i', '', $sqlContent);
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            $sqlContent = preg_replace('/PRAGMA\s+foreign_keys\s*=\s*(OFF|ON)\s*;/i', '', $sqlContent);
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Split into individual SQL statements
        $rawQueries = explode(";", $sqlContent);

        try {
            foreach ($rawQueries as $rawQuery) {
                // Strip line comments starting with --
                $lines = array_filter(explode("\n", $rawQuery), function ($line) {
                    return !str_starts_with(trim($line), '--');
                });

                $cleanQuery = trim(implode("\n", $lines));

                if (!empty($cleanQuery)) {
                    DB::unprepared($cleanQuery);
                }
            }

            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            return back()->with('success', 'Database berhasil di-import & di-restore dari file backup!');
        } catch (\Throwable $e) {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
            return back()->with('error', 'Gagal meng-import database SQL: ' . $e->getMessage());
        }
    }
}
