<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseAuditController extends Controller
{
    /**
     * فحص الاختلافات فقط
     */
    public function checkFixes()
    {
        $db = DB::connection()->getDatabaseName();
        $tables = DB::select("SHOW TABLES");
        $key = "Tables_in_{$db}";

        $existingTables = [];
        foreach ($tables as $t) {
            $existingTables[] = $t->$key;
        }

        $fixes = [];
        $migrationTables = $this->getMigrationTables();

        // فحص الجداول المفقودة
        foreach ($migrationTables as $tableName => $columns) {
            if (!in_array($tableName, $existingTables)) {
                $fixes[$tableName] = [
                    'type' => 'missing_table',
                    'columns' => $columns
                ];
            }
        }

        // فحص الأعمدة المفقودة
        foreach ($existingTables as $table) {
            if (in_array($table, ['migrations', 'failed_jobs', 'password_resets'])) {
                continue;
            }

            if (isset($migrationTables[$table])) {
                $dbColumns = DB::select("SHOW COLUMNS FROM `{$table}`");
                $missingColumns = [];

                foreach ($migrationTables[$table] as $migCol => $migDetails) {
                    $found = false;
                    foreach ($dbColumns as $col) {
                        if ($col->Field === $migCol) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found && !in_array($migCol, ['id', 'created_at', 'updated_at'])) {
                        $missingColumns[] = [
                            'name' => $migCol,
                            'type' => $migDetails['type'],
                            'nullable' => $migDetails['nullable']
                        ];
                    }
                }

                if (!empty($missingColumns)) {
                    $fixes[$table] = [
                        'type' => 'missing_columns',
                        'columns' => $missingColumns
                    ];
                }
            }
        }

        return response()->json([
            'has_fixes' => !empty($fixes),
            'count' => count($fixes),
            'fixes' => $fixes
        ]);
    }

    /**
     * تطبيق الإصلاحات
     */
    public function applyFixes()
    {
        $db = DB::connection()->getDatabaseName();
        $tables = DB::select("SHOW TABLES");
        $key = "Tables_in_{$db}";

        $existingTables = [];
        foreach ($tables as $t) {
            $existingTables[] = $t->$key;
        }

        $applied = [];
        $migrationTables = $this->getMigrationTables();

        // إنشاء الجداول المفقودة
        foreach ($migrationTables as $tableName => $columns) {
            if (!in_array($tableName, $existingTables)) {
                $result = $this->createTable($tableName, $columns);
                $applied[] = $result;
            }
        }

        // إضافة الأعمدة المفقودة
        foreach ($existingTables as $table) {
            if (in_array($table, ['migrations', 'failed_jobs', 'password_resets'])) {
                continue;
            }

            if (isset($migrationTables[$table])) {
                $dbColumns = DB::select("SHOW COLUMNS FROM `{$table}`");

                foreach ($migrationTables[$table] as $migCol => $migDetails) {
                    $found = false;
                    foreach ($dbColumns as $col) {
                        if ($col->Field === $migCol) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found && !in_array($migCol, ['id', 'created_at', 'updated_at'])) {
                        $result = $this->addColumn($table, $migCol, $migDetails);
                        $applied[] = $result;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'applied' => $applied,
            'count' => count($applied),
            'message' => 'تم تطبيق التغييرات بنجاح'
        ]);
    }

    /**
     * قراءة الجداول من ملفات الـ migrations
     */
    private function getMigrationTables()
    {
        $migrationPath = database_path('migrations');
        $migrationFiles = File::files($migrationPath);

        $migrationTables = [];

        foreach ($migrationFiles as $file) {
            $content = File::get($file);

            preg_match_all("/Schema::create\s*\(\s*['\"](\w+)['\"]/", $content, $creates);
            preg_match_all("/\\\$table->(\w+)\s*\(\s*['\"](\w+)['\"]([^;]*)/", $content, $columns);

            if (!empty($creates[1])) {
                foreach ($creates[1] as $tableName) {
                    if (!isset($migrationTables[$tableName])) {
                        $migrationTables[$tableName] = [];
                    }
                }
            }

            if (!empty($columns[2])) {
                foreach ($columns[2] as $index => $columnName) {
                    $type = $columns[1][$index];
                    $extra = $columns[3][$index];

                    foreach ($creates[1] as $tableName) {
                        if (!isset($migrationTables[$tableName][$columnName])) {
                            $migrationTables[$tableName][$columnName] = [
                                'type' => $type,
                                'nullable' => strpos($extra, 'nullable') !== false,
                            ];
                        }
                    }
                }
            }
        }

        return $migrationTables;
    }

    /**
     * إنشاء جدول جديد
     */
    private function createTable($tableName, $columns)
    {
        try {
            $sql = "CREATE TABLE `{$tableName}` (";
            $sql .= "`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,";

            foreach ($columns as $colName => $colDetails) {
                if (in_array($colName, ['id', 'created_at', 'updated_at'])) {
                    continue;
                }

                $sqlType = $this->getSqlType($colDetails['type']);
                $nullable = $colDetails['nullable'] ? 'NULL' : 'NOT NULL';

                $sql .= "`{$colName}` {$sqlType} {$nullable},";
            }

            $sql .= "`created_at` TIMESTAMP NULL,";
            $sql .= "`updated_at` TIMESTAMP NULL";
            $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            DB::statement($sql);

            return [
                'table' => $tableName,
                'action' => 'table_created',
                'columns_count' => count($columns),
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'table' => $tableName,
                'action' => 'table_created',
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * إضافة عمود لجدول موجود
     */
    private function addColumn($table, $columnName, $details)
    {
        try {
            $sqlType = $this->getSqlType($details['type']);
            $nullable = $details['nullable'] ? 'NULL' : 'NOT NULL';

            DB::statement("ALTER TABLE `{$table}` ADD `{$columnName}` {$sqlType} {$nullable}");

            return [
                'table' => $table,
                'column' => $columnName,
                'action' => 'column_added',
                'type' => $details['type'],
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'table' => $table,
                'column' => $columnName,
                'action' => 'column_added',
                'type' => $details['type'],
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * تحويل نوع Laravel إلى نوع SQL
     */
    private function getSqlType($type)
    {
        return match($type) {
            'string' => 'VARCHAR(255)',
            'text' => 'TEXT',
            'integer' => 'INT',
            'bigInteger' => 'BIGINT',
            'boolean' => 'TINYINT(1)',
            'date' => 'DATE',
            'datetime' => 'DATETIME',
            'timestamp' => 'TIMESTAMP',
            'decimal' => 'DECIMAL(8,2)',
            'json' => 'JSON',
            'longText' => 'LONGTEXT',
            'tinyInteger' => 'TINYINT',
            'smallInteger' => 'SMALLINT',
            'mediumInteger' => 'MEDIUMINT',
            'float' => 'FLOAT',
            'double' => 'DOUBLE',
            default => 'VARCHAR(255)'
        };
    }
}
