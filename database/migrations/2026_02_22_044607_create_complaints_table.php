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
    Schema::create('complaints', function (Blueprint $table) {
        $table->id('complaint_id');
        $table->unsignedBigInteger('user_id'); // مقدم الشكوى
        $table->string('title', 150);
        $table->text('description');
        $table->enum('status', ['pending', 'in_progress', 'resolved'])->default('pending');
        $table->timestamp('resolved_at')->nullable(); // تاريخ الحل
        $table->timestamps(); // تُنشئ تاريخ الإنشاء وتاريخ التحديث

        // العلاقة
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
