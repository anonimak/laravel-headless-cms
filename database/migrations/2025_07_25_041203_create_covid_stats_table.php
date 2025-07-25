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
        Schema::create('covid_stats', function (Blueprint $table) {
            $table->id();
            $table->string('region_name'); // 'Indonesia' atau 'Global'
            $table->string('region_iso')->nullable(); // 'IDN' atau null untuk global
            $table->date('date');
            $table->bigInteger('confirmed');
            $table->bigInteger('deaths');
            $table->bigInteger('recovered');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('covid_stats');
    }
};
