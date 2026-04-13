<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_opportunities', function (Blueprint $table) {
            $table->string('department', 150)->nullable()->after('title');
            $table->string('city', 120)->nullable()->after('available_seats');
            $table->json('custom_questions')->nullable()->after('city');
        });

        DB::statement("
            ALTER TABLE training_opportunities
            ADD COLUMN status ENUM('active', 'closed') NOT NULL DEFAULT 'active' AFTER custom_questions
        ");

        DB::statement("
            UPDATE training_opportunities
            SET status = CASE WHEN is_active = 1 THEN 'active' ELSE 'closed' END
        ");
    }

    public function down(): void
    {
        Schema::table('training_opportunities', function (Blueprint $table) {
            $table->dropColumn(['department', 'city', 'custom_questions', 'status']);
        });
    }
};
