<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('order_id')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->string('package')->nullable();
            $table->string('profile')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->string('wifi_token')->nullable();
            $table->string('wifi_password')->nullable();
            $table->string('client_mac')->nullable()->index();
            $table->string('client_ip')->nullable();
            $table->string('payment_method')->default('palmpesa');
            $table->json('payment_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('hotspot_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('mac_address')->index();
            $table->string('ip_address')->nullable();
            $table->string('username')->nullable();
            $table->string('package')->nullable();
            $table->string('profile')->nullable();
            $table->integer('total_seconds')->default(0);
            $table->integer('used_seconds')->default(0);
            $table->string('status')->default('active');
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('disconnected_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'connected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotspot_sessions');
        Schema::dropIfExists('payments');
    }
};
