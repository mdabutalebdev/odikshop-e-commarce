<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create missing tables
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('logo')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('show_on_home')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('location')->nullable();
                $table->string('avatar')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('text');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->string('gateway')->nullable();
                $table->string('pp_id')->nullable();
                $table->string('transaction_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('status')->default('pending');
                $table->json('raw_response')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add columns to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_admin')) $table->boolean('is_admin')->default(false)->after('password');
            if (!Schema::hasColumn('users', 'phone')) $table->string('phone')->nullable()->after('is_admin');
            if (!Schema::hasColumn('users', 'address')) $table->string('address')->nullable()->after('phone');
            if (!Schema::hasColumn('users', 'city')) $table->string('city')->nullable()->after('address');
            if (!Schema::hasColumn('users', 'avatar')) $table->string('avatar')->nullable()->after('email');
        });

        // 3. Add columns to categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'show_on_home')) $table->boolean('show_on_home')->default(false);
        });

        // 4. Add columns to products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand_id')) $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
            if (!Schema::hasColumn('products', 'is_best_seller')) $table->boolean('is_best_seller')->default(false);
            if (!Schema::hasColumn('products', 'is_new_arrival')) $table->boolean('is_new_arrival')->default(false);
            if (!Schema::hasColumn('products', 'top_selling')) $table->boolean('top_selling')->default(false);
            if (!Schema::hasColumn('products', 'main_image')) $table->string('main_image')->nullable();
            if (!Schema::hasColumn('products', 'short_description')) $table->text('short_description')->nullable();
            if (!Schema::hasColumn('products', 'attributes')) $table->json('attributes')->nullable();
        });

        // 5. Add columns to order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'options')) $table->json('options')->nullable();
        });

        // 6. Add columns to banners
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'subtitle')) $table->string('subtitle')->nullable();
            if (!Schema::hasColumn('banners', 'badge_text')) $table->string('badge_text')->nullable();
            if (!Schema::hasColumn('banners', 'button_text')) $table->string('button_text')->nullable();
        });
    }

    public function down(): void
    {
        // For brevity, we don't drop everything here, but in production we should
    }
};
