<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_opportunities', function (Blueprint $table) {
            $table->enum('training_type', ['summer', 'cooperative'])->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('training_opportunities', function (Blueprint $table) {
            $table->dropColumn('training_type');
        });
    }
};
