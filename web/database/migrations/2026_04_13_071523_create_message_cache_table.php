<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('message_cache', function (Blueprint $table) {
            $table->id();
            $table->string('sender_number');
            $table->text('latest_message');
            $table->timestamps(); // opsional, tapi disarankan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_cache');
    }
};