<?php

use App\Support\EmailReplySchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        EmailReplySchema::ensure();
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
