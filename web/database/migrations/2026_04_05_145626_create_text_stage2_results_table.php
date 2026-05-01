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
        Schema::create('stage2_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->float('time_credibility')->nullable();
            $table->float('title_credibility')->nullable();
            $table->float('mean_contradiction')->nullable();
            $table->float('mean_entailment')->nullable();
            $table->float('std_contradiction')->nullable();
            $table->json('url')->nullable();
            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage2_results');
    }
};
