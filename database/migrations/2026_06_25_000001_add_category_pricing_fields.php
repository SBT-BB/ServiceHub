<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('weekend_surcharge_percent', 5, 2)->default(0)->after('base_fare');
            $table->decimal('month_end_surcharge_percent', 5, 2)->default(0)->after('weekend_surcharge_percent');
            $table->decimal('peak_time_surcharge_percent', 5, 2)->default(0)->after('month_end_surcharge_percent');
            $table->string('peak_time_start')->nullable()->after('peak_time_surcharge_percent');
            $table->string('peak_time_end')->nullable()->after('peak_time_start');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['weekend_surcharge_percent', 'month_end_surcharge_percent', 'peak_time_surcharge_percent', 'peak_time_start', 'peak_time_end']);
        });
    }
};
