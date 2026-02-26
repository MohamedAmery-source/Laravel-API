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
    Schema::create('training_requests', function (Blueprint $table) {
        $table->id('request_id');
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('opportunity_id');
        $table->date('submission_date');
        $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'completed'])->default('pending');
        $table->text('student_notes')->nullable();
        $table->text('admin_notes')->nullable();
        $table->text('institution_notes')->nullable();
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // العلاقات
        $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        $table->foreign('opportunity_id')->references('opportunity_id')->on('training_opportunities')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_requests');
    }
};
