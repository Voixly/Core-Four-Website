<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LeadTypeColumn
{
    public static function ensure(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true) || ! Schema::hasTable('leads')) {
            return;
        }

        $column = DB::selectOne("SHOW COLUMNS FROM `leads` WHERE Field = 'type'");
        $definition = strtolower((string) ($column->Type ?? ''));
        if ($definition === '' || str_contains($definition, 'coatings') || str_starts_with($definition, 'varchar')) {
            return;
        }

        DB::statement("ALTER TABLE `leads` MODIFY `type` VARCHAR(32) NOT NULL DEFAULT 'residential'");
    }
}
