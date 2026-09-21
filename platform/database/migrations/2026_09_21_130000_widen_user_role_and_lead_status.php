<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(24) NOT NULL DEFAULT 'staff'");
            DB::statement("ALTER TABLE leads MODIFY status VARCHAR(24) NOT NULL DEFAULT 'new'");

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 24)->default('staff')->change();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('status', 24)->default('new')->change();
        });
    }

    public function down(): void
    {
        // Values stay compatible as strings.
    }
};
