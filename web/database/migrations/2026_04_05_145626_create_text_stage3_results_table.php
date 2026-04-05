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
        Schema::create('text_stage3_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('request_id')->constrained('text_requests')->cascadeOnDelete();
    $table->string('title')->nullable();
    $table->float('max_similarity')->nullable();
    $table->integer('similar_count')->nullable();
    $table->text('reasoning')->nullable();
    $table->json('detail_similarities')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_stage3_results');
    }
};
