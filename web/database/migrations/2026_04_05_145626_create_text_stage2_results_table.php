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
        Schema::create('text_stage2_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('request_id')->constrained('text_requests')->cascadeOnDelete();
    $table->string('article_title')->nullable();
    $table->string('article_url')->nullable();
    $table->date('article_date')->nullable();
    $table->text('article_content')->nullable();
    $table->text('chunk_text')->nullable();
    $table->float('similarity_score')->nullable();
    $table->float('nli_score')->nullable();
    $table->string('label')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_stage2_results');
    }
};
