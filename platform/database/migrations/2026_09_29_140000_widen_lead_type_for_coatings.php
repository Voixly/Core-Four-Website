<?php

use App\Support\LeadTypeColumn;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        LeadTypeColumn::ensure();
    }

    public function down(): void
    {
        // Coatings rows would not fit back into the original residential/commercial list.
    }
};
