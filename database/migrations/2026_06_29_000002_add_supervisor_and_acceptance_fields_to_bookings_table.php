<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * बुकिंग्स टेबल में सुपरवाइज़र असाइनमेंट और वेंडर/सुपरवाइज़र स्वीकृति स्टेटस जोड़ना।
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // सुपरवाइज़र जो शिफ्टिंग का काम करेगा
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('vendor_id');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');

            // वेंडर ने बुकिंग स्वीकार की या नहीं
            $table->enum('vendor_acceptance_status', ['pending', 'accepted', 'rejected'])
                  ->default('pending')
                  ->after('supervisor_id');

            // सुपरवाइज़र ने बुकिंग स्वीकार की या नहीं
            $table->enum('supervisor_acceptance_status', ['pending', 'accepted', 'rejected'])
                  ->default('pending')
                  ->after('vendor_acceptance_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['supervisor_id', 'vendor_acceptance_status', 'supervisor_acceptance_status']);
        });
    }
};
