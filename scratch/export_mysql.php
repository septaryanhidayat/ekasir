<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'users',
    'tenants',
    'products',
    'expenses',
    'cash_registers',
    'cash_flows',
    'transactions',
    'transaction_details',
    'migrations',
];

$output = "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) continue;

    $rows = DB::table($table)->get();
    if ($rows->isEmpty()) continue;

    $output .= "-- Data for table `$table` --\n";
    $output .= "DELETE FROM `$table`;\n";

    foreach ($rows as $row) {
        $rowArray = (array)$row;
        $keys = array_keys($rowArray);
        $fields = implode('`, `', $keys);
        
        $values = array_map(function($val) {
            if (is_null($val)) return 'NULL';
            if (is_bool($val)) return $val ? '1' : '0';
            if (is_numeric($val) && !is_string($val)) return $val;
            return DB::connection()->getPdo()->quote((string)$val);
        }, array_values($rowArray));

        $valStr = implode(', ', $values);
        $output .= "INSERT INTO `$table` (`$fields`) VALUES ($valStr);\n";
    }
    $output .= "\n";
}

$output .= "SET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents(base_path('database/export_data_mysql.sql'), $output);
echo "SQL export generated at database/export_data_mysql.sql (" . strlen($output) . " bytes)\n";
