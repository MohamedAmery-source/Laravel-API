<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_reports', function (Blueprint $table) {
            $table->integer('week_number')->nullable()->after('report_file');
        });
    }

    public function down(): void
    {
        Schema::table('training_reports', function (Blueprint $table) {
            $table->dropColumn('week_number');
        });
    }
};
