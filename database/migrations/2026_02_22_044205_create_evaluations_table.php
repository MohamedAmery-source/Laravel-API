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
    Schema::create('evaluations', function (Blueprint $table) {
        $table->id('evaluation_id');
        $table->unsignedBigInteger('internship_id');
        $table->enum('evaluator_type', ['institution', 'supervisor', 'student']); // من قام بالتقييم
        $table->integer('technical_skills')->nullable(); // المهارات التقنية
        $table->integer('commitment')->nullable(); // الالتزام
        $table->integer('teamwork')->nullable(); // العمل الجماعي
        $table->integer('attendance')->nullable(); // الحضور
        $table->integer('overall_rating')->nullable(); // التقييم العام
        $table->text('comments')->nullable();
        $table->date('evaluation_date')->nullable();
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // العلاقة
        $table->foreign('internship_id')->references('internship_id')->on('internships')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
