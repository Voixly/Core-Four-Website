<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobNimbusSchema
{
    private static bool $ready = false;

    public static function ensure(): void
    {
        if (self::$ready) {
            return;
        }

        if (Schema::hasTable('leads') && ! Schema::hasColumn('leads', 'jobnimbus_contact_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->string('jobnimbus_contact_id', 40)->nullable();
            });
        }

        if (Schema::hasTable('reviews') && ! Schema::hasColumn('reviews', 'jobnimbus_job_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('jobnimbus_job_id', 40)->nullable()->unique();
            });
        }

        self::$ready = true;
    }
}
