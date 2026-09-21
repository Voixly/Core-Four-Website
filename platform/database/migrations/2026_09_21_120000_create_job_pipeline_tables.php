<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('customer_invites');
        Schema::dropIfExists('job_documents');
        Schema::dropIfExists('job_document_requests');
        Schema::dropIfExists('job_events');
        Schema::dropIfExists('job_contacts');
        Schema::dropIfExists('roofing_jobs');
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('pipelines');

        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('audience', ['residential', 'commercial'])->index();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('customer_label');
            $table->string('slug');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('customer_visible')->default(false);
            $table->boolean('notify_customer')->default(false);
            $table->string('outcome', 16)->default('open');
            $table->timestamps();
            $table->unique(['pipeline_id', 'slug']);
        });

        Schema::create('roofing_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('number', 24)->unique();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pipeline_id')->constrained();
            $table->foreignId('stage_id')->constrained('pipeline_stages');
            $table->enum('type', ['residential', 'commercial'])->default('residential');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 16)->nullable();
            $table->string('payment_path', 40)->default('retail');
            $table->string('urgency', 24)->default('standard');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('customer_summary')->nullable();
            $table->string('status', 24)->default('open')->index();
            $table->timestamps();
            $table->index(['pipeline_id', 'stage_id']);
        });

        Schema::create('job_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 24)->default('homeowner');
            $table->timestamps();
            $table->unique(['job_id', 'user_id']);
        });

        Schema::create('job_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event');
            $table->text('body')->nullable();
            $table->boolean('customer_visible')->default(false);
            $table->timestamps();
            $table->index(['job_id', 'created_at']);
        });

        Schema::create('job_document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('category', 40);
            $table->string('label');
            $table->boolean('required')->default(true);
            $table->unsignedBigInteger('fulfilled_by')->nullable();
            $table->timestamps();
        });

        Schema::create('job_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('job_document_requests')->nullOnDelete();
            $table->string('category', 40)->default('other');
            $table->string('path');
            $table->string('original_name');
            $table->string('visibility', 16)->default('staff');
            $table->timestamps();
        });

        Schema::table('job_document_requests', function (Blueprint $table) {
            $table->foreign('fulfilled_by')->references('id')->on('job_documents')->nullOnDelete();
        });

        Schema::create('customer_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('roofing_jobs')->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('contact_role', 24)->default('homeowner');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('job_id')->nullable()->constrained('roofing_jobs')->nullOnDelete();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('agency','owner','staff','customer') NOT NULL DEFAULT 'staff'");
            DB::statement("ALTER TABLE leads MODIFY status ENUM('new','contacted','inspected','bid','won','lost','active') NOT NULL DEFAULT 'new'");
        }
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_id');
        });
        Schema::dropIfExists('customer_invites');
        Schema::table('job_document_requests', function (Blueprint $table) {
            $table->dropForeign(['fulfilled_by']);
        });
        Schema::dropIfExists('job_documents');
        Schema::dropIfExists('job_document_requests');
        Schema::dropIfExists('job_events');
        Schema::dropIfExists('job_contacts');
        Schema::dropIfExists('roofing_jobs');
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('pipelines');
    }
};
