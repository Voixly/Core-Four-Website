<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailReplySchema
{
    private static bool $ready = false;

    public static function ensure(): void
    {
        if (self::$ready || ! Schema::hasTable('leads') || ! Schema::hasTable('conversations') || ! Schema::hasTable('messages')) {
            return;
        }

        self::add('leads', 'reply_token', function (Blueprint $table) {
            $table->string('reply_token', 32)->nullable()->unique();
        });
        self::add('conversations', 'channel', function (Blueprint $table) {
            $table->string('channel', 16)->default('chat')->index();
        });
        self::add('conversations', 'subject', function (Blueprint $table) {
            $table->string('subject')->nullable();
        });
        self::add('conversations', 'reply_token', function (Blueprint $table) {
            $table->string('reply_token', 32)->nullable()->index();
        });
        self::add('conversations', 'awaiting_staff', function (Blueprint $table) {
            $table->boolean('awaiting_staff')->default(false);
        });
        self::add('messages', 'external_id', function (Blueprint $table) {
            $table->string('external_id', 64)->nullable()->unique();
        });
        self::add('messages', 'message_id', function (Blueprint $table) {
            $table->string('message_id', 255)->nullable()->unique();
        });

        self::$ready = true;
    }

    private static function add(string $table, string $column, \Closure $define): void
    {
        if (Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, $define);
    }
}
