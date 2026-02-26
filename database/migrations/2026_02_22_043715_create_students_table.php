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
    Schema::create('students', function (Blueprint $table) {
        $table->id('student_id');
        $table->unsignedBigInteger('user_id')->unique(); // ربط مع جدول المستخدمين
        $table->string('student_number', 20)->unique(); // الرقم الجامعي
        $table->string('department', 100); // التخصص
        $table->string('level', 10); // المستوى
        $table->decimal('gpa', 3, 2)->nullable(); // المعدل التراكمي
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // العلاقة
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
