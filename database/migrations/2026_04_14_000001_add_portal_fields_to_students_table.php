<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('university', 150)->nullable()->after('student_number');
            $table->string('city', 100)->nullable()->after('gpa');
            $table->json('skills')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['university', 'city', 'skills']);
        });
    }
};
