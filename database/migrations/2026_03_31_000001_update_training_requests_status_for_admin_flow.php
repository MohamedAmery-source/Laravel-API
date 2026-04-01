<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE training_requests
            MODIFY COLUMN status ENUM(
                'pending',
                'pending_admin',
                'pending_institution',
                'under_review',
                'approved',
                'rejected',
                'completed'
            ) NOT NULL DEFAULT 'pending_admin'
        ");

        DB::statement("
            UPDATE training_requests
            SET status = 'pending_admin'
            WHERE status = 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE training_requests
            SET status = 'pending'
            WHERE status IN ('pending_admin', 'pending_institution')
        ");

        DB::statement("
            ALTER TABLE training_requests
            MODIFY COLUMN status ENUM(
                'pending',
                'under_review',
                'approved',
                'rejected',
                'completed'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
