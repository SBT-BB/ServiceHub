<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('loading_charge',   10, 2)->default(0.00)->after('month_end_charges');
            $table->decimal('unloading_charge', 10, 2)->default(0.00)->after('loading_charge');
            $table->decimal('packing_charge',   10, 2)->default(0.00)->after('unloading_charge');
            $table->decimal('labour_charge',    10, 2)->default(0.00)->after('packing_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['loading_charge', 'unloading_charge', 'packing_charge', 'labour_charge']);
        });
    }
};
