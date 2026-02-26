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
    Schema::create('internships', function (Blueprint $table) {
        $table->id('internship_id');
        $table->unsignedBigInteger('request_id')->unique(); // ربط مع الطلب المقبول
        $table->date('actual_start_date')->nullable();
        $table->date('actual_end_date')->nullable();
        $table->string('mentor_name', 100)->nullable(); // اسم المدرب في المؤسسة
        $table->text('assigned_tasks')->nullable();
        $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // العلاقة
        $table->foreign('request_id')->references('request_id')->on('training_requests')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
