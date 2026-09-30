<?php

use App\Support\JobNimbusSchema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        JobNimbusSchema::ensure();
    }

    public function down(): void
    {
        // The sync ids are left in place so a review is not imported twice.
    }
};
