<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE leads MODIFY type ENUM('residential', 'commercial', 'coatings') NOT NULL DEFAULT 'residential'");
    }

    public function down(): void
    {
        // Existing coatings rows would not fit back into the old list.
    }
};
