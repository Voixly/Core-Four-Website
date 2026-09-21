<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->enum('type', ['residential', 'commercial'])->default('residential');
            $table->string('job')->nullable();
            $table->unsignedTinyInteger('stars')->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 24)->default('pending')->index();
            $table->string('source', 40)->default('public');
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rated_at')->nullable();
            $table->timestamp('google_clicked_at')->nullable();
            $table->timestamp('yelp_clicked_at')->nullable();
            $table->text('recovery_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
