<?php

use App\Support\ReviewMailSchema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ReviewMailSchema::ensure();
    }

    public function down(): void
    {
        // The send log stays so a count is not wiped by a rollback.
    }
};
