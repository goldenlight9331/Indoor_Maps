<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kiosks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('levelid');
            $table->timestamps(false);
        });
        //     DB::statement("
        //     SELECT AddGeometryColumn('kiosks', 'geom', 4326, 'POINT', 'XY')
        // ");
        DB::statement('ALTER TABLE kiosks ADD COLUMN geom geometry(Point, 4326)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosks');
    }
};