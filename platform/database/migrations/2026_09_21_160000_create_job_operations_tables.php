<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roofing_jobs', function (Blueprint $table) {
            $table->string('roof_type', 40)->nullable();
            $table->decimal('squares', 8, 2)->nullable();
            $table->unsignedTinyInteger('stories')->nullable();
            $table->string('pitch', 24)->nullable();
            $table->string('material_system', 80)->nullable();
            $table->string('insurance_carrier')->nullable();
            $table->string('claim_number', 80)->nullable();
            $table->string('hoa_name')->nullable();
            $table->text('access_notes')->nullable();
            $table->string('crew_name')->nullable();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('job_id')->nullable()->after('lead_id')->constrained('roofing_jobs')->nullOnDelete();
        });

        Schema::create('job_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 32)->default('inspection');
            $table->string('status', 24)->default('scheduled');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['starts_at', 'status']);
        });

        Schema::create('job_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('title');
            $table->string('status', 24)->default('draft');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('job_quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('job_quotes')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('qty', 10, 2)->default(1);
            $table->string('unit', 24)->default('ea');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('job_change_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status', 24)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('number', 32);
            $table->string('kind', 24)->default('deposit');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status', 24)->default('draft');
            $table->date('due_on')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('qty', 10, 2)->default(1);
            $table->string('unit', 24)->default('ea');
            $table->string('status', 24)->default('needed');
            $table->string('vendor')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->date('due_on')->nullable();
            $table->timestamps();
        });

        Schema::create('job_warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('kind', 32)->default('workmanship');
            $table->string('manufacturer')->nullable();
            $table->string('registration', 80)->nullable();
            $table->date('starts_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_cost_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('kind', 24)->default('material');
            $table->string('label');
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_cost_lines');
        Schema::dropIfExists('job_warranties');
        Schema::dropIfExists('job_tasks');
        Schema::dropIfExists('job_materials');
        Schema::dropIfExists('job_invoices');
        Schema::dropIfExists('job_change_orders');
        Schema::dropIfExists('job_quote_items');
        Schema::dropIfExists('job_quotes');
        Schema::dropIfExists('job_appointments');

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_id');
        });

        Schema::table('roofing_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'roof_type', 'squares', 'stories', 'pitch', 'material_system',
                'insurance_carrier', 'claim_number', 'hoa_name', 'access_notes', 'crew_name',
            ]);
        });
    }
};
