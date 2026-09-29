<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('reply_token', 32)->nullable()->unique();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->string('channel', 16)->default('chat')->index();
            $table->string('subject')->nullable();
            $table->string('reply_token', 32)->nullable()->index();
            $table->boolean('awaiting_staff')->default(false);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->string('external_id', 64)->nullable()->unique();
            $table->string('message_id', 255)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'message_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['channel', 'subject', 'reply_token', 'awaiting_staff']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('reply_token');
        });
    }
};
