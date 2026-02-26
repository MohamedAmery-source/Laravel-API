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
    Schema::create('user_roles', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('role_id');
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamp('assigned_at')->useCurrent(); // تاريخ التعيين
        $table->timestamps();

        // ربط العلاقات (Foreign Keys)
        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
        
        $table->primary(['user_id', 'role_id']); // المفتاح الأساسي مركب
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
