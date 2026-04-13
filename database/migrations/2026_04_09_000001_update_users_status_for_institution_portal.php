<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN status ENUM(
                'active',
                'inactive',
                'pending_approval',
                'suspended'
            ) NOT NULL DEFAULT 'active'
        ");

        DB::statement("
            UPDATE users
            SET status = 'pending_approval'
            WHERE user_type = 'institution' AND status = 'inactive'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE users
            SET status = 'inactive'
            WHERE status = 'pending_approval'
        ");

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN status ENUM(
                'active',
                'inactive',
                'suspended'
            ) NOT NULL DEFAULT 'active'
        ");
    }
};
