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
    Schema::create('notifications', function (Blueprint $table) {
        $table->id('notification_id');
        $table->unsignedBigInteger('user_id'); // لمن هذا الإشعار
        $table->text('message');
        $table->string('notification_type', 50)->nullable();
        $table->unsignedBigInteger('related_request_id')->nullable(); // في حال كان الإشعار مرتبط بطلب
        $table->boolean('is_read')->default(false); // تم القراءة أم لا
        $table->timestamps();

        // العلاقات
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        $table->foreign('related_request_id')->references('request_id')->on('training_requests')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
