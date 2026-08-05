<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mikrotik_settings', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('username');
            $table->string('password');
            $table->integer('port')->default(8728);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrotik_settings');
    }
};
