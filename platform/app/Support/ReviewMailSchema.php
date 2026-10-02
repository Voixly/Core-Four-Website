<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReviewMailSchema
{
    private static bool $ready = false;

    public static function ensure(): void
    {
        if (self::$ready || ! Schema::hasTable('reviews')) {
            return;
        }

        if (! Schema::hasTable('review_mail_logs')) {
            Schema::create('review_mail_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->nullable()->constrained()->nullOnDelete();
                $table->string('email');
                $table->string('subject')->nullable();
                $table->string('status', 16)->default('sent')->index();
                $table->text('error')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }

        self::$ready = true;
    }
}
