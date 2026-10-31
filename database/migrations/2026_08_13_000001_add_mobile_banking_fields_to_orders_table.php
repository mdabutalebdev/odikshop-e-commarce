<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Manual mobile-banking (bKash / Nagad) payment details supplied by the
            // customer at checkout so the shop owner can verify the transaction.
            $table->string('payment_channel')->nullable()->after('payment_method');        // bkash | nagad
            $table->string('payment_sender_number')->nullable()->after('payment_channel');  // customer's wallet number
            $table->string('payment_txn_id')->nullable()->after('payment_sender_number');   // transaction id (TrxID)
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_channel', 'payment_sender_number', 'payment_txn_id']);
        });
    }
};
