<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fornt_ends', function (Blueprint $table) {
            $table->id();
            $table->binary('home')->nullable();
            $table->binary('navigation')->nullable();
            $table->binary('more')->nullable();
            $table->timestamps(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornt_ends');
    }
};