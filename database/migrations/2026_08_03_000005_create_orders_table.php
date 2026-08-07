<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Delivery address (guest checkout supported)
            $table->string('name');
            $table->string('phone');
            $table->string('division')->nullable();
            $table->string('district')->nullable();
            $table->string('thana')->nullable();
            $table->text('address');
            $table->text('notes')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->string('delivery_zone')->default('inside_dhaka'); // inside_dhaka | outside_dhaka
            $table->string('payment_method')->default('cod');         // cod | mobile_banking
            $table->string('payment_status')->default('unpaid');      // unpaid | paid | failed
            $table->string('status')->default('pending');             // pending|processing|shipped|delivered|cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
