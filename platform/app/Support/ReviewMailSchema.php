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
                $table->unsignedBigInteger('email_step_id')->nullable()->index();
                $table->string('email');
                $table->string('subject')->nullable();
                $table->string('status', 16)->default('sent')->index();
                $table->text('error')->nullable();
                $table->timestamp('scheduled_at')->nullable()->index();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('review_mail_logs') && ! Schema::hasColumn('review_mail_logs', 'scheduled_at')) {
            Schema::table('review_mail_logs', function (Blueprint $table) {
                $table->timestamp('scheduled_at')->nullable()->index();
            });
        }

        if (Schema::hasTable('review_mail_logs') && ! Schema::hasColumn('review_mail_logs', 'email_step_id')) {
            Schema::table('review_mail_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('email_step_id')->nullable()->index();
            });
        }

        self::$ready = true;
    }
}
