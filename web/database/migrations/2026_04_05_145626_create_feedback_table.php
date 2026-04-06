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
        Schema::create('feedbacks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
    $table->foreignId('text_request_id')->nullable()->constrained('text_requests')->cascadeOnDelete();
    $table->foreignId('image_request_id')->nullable()->constrained('image_requests')->cascadeOnDelete();
    $table->string('feedback')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
