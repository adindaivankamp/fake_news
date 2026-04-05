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
        Schema::create('text_stage1_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('request_id')->constrained('text_requests')->cascadeOnDelete();
    $table->foreignId('knowledge_id')->constrained('knowledge_base')->cascadeOnDelete();
    $table->float('nli_score')->nullable();
    $table->float('similarity_score')->nullable();
    $table->string('label')->nullable();
    $table->boolean('is_stop')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_stage1_results');
    }
};
