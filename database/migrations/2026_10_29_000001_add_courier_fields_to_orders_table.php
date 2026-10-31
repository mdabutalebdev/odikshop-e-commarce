<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Steadfast consignment details, filled in when a parcel is booked.
            $table->string('courier_consignment_id')->nullable()->after('payment_status');
            $table->string('courier_tracking_code')->nullable()->after('courier_consignment_id');
            $table->string('courier_status')->nullable()->after('courier_tracking_code');
            $table->timestamp('courier_booked_at')->nullable()->after('courier_status');
            $table->string('courier_note')->nullable()->after('courier_booked_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'courier_consignment_id',
                'courier_tracking_code',
                'courier_status',
                'courier_booked_at',
                'courier_note',
            ]);
        });
    }
};
