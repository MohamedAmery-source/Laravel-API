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
    Schema::create('training_reports', function (Blueprint $table) {
        $table->id('report_id');
        $table->unsignedBigInteger('internship_id');
        $table->string('title', 200)->nullable();
        $table->text('content')->nullable();
        $table->string('report_file', 255)->nullable();
        $table->enum('submitted_by', ['student', 'institution', 'supervisor']); // مقدم التقرير
        $table->date('submission_date')->nullable();
        $table->boolean('is_approved')->default(false);
        $table->text('supervisor_comments')->nullable();
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
        Schema::dropIfExists('training_reports');
    }
};
