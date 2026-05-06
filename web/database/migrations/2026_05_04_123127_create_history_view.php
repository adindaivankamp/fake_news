<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW history_view AS
            SELECT 
                ui.id AS interaction_id,
                u.id AS user_id,
                r.id AS request_id, 
                u.name AS username,
                r.input_text,
                r.created_at,
                r.final_label,
                r.final_confidence,
                r.status
            FROM 
                requests r
            JOIN 
                user_interactions ui ON r.id = ui.request_id
            JOIN 
                users u ON ui.user_id = u.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS history_view");
    }
};