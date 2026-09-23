<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roofing_jobs', function (Blueprint $table) {
            $table->string('jobnimbus_contact_id', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('roofing_jobs', function (Blueprint $table) {
            $table->dropColumn('jobnimbus_contact_id');
        });
    }
};
