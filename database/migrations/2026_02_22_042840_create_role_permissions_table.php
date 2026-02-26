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
    Schema::create('role_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('role_id');
        $table->unsignedBigInteger('permission_id');
        $table->boolean('granted')->default(true); // منح / سحب
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // ربط العلاقات
        $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
        $table->foreign('permission_id')->references('permission_id')->on('permissions')->onDelete('cascade');
        
        $table->primary(['role_id', 'permission_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
