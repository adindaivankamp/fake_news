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
    Schema::table('requests', function (Blueprint $table) {
        $table->text('clean_text')->nullable()->after('input_text');
    });

    Schema::table('knowledge_base', function (Blueprint $table) {
        $table->json('url')->nullable()->after('source_url');
    });

    Schema::table('image_search_results', function (Blueprint $table) {
        $table->json('source_url')->nullable()->change();
        $table->float('mean_date_score')->nullable()->after('similarity_score');
    });

    Schema::table('stage1_results', function (Blueprint $table) {
        $table->foreignId('image_results_id')->nullable()->after('id')->constrained('image_results');
    });

    Schema::table('stage2_results', function (Blueprint $table) {
        $table->float('time_credibility')->nullable();
        $table->float('title_credibility')->nullable();
        $table->float('mean_entailment')->nullable();
        $table->float('mean_contradiction')->nullable();
        $table->float('std_contradiction')->nullable();
        $table->json('url')->nullable();
    });

}   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
