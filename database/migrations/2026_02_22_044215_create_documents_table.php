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
    Schema::create('documents', function (Blueprint $table) {
        $table->id('document_id');
        $table->unsignedBigInteger('user_id'); // من قام برفع الملف
        $table->unsignedBigInteger('request_id')->nullable(); // قد يكون مرتبطاً بطلب تدريب
        $table->string('title', 150)->nullable();
        $table->string('file_url', 255); // مسار الملف
        $table->string('file_type', 50)->nullable(); // نوع الملف pdf, png...
        $table->boolean('is_active')->default(true);
        $table->timestamps(); // تغني عن حقل uploaded_at

        // العلاقات
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        $table->foreign('request_id')->references('request_id')->on('training_requests')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
