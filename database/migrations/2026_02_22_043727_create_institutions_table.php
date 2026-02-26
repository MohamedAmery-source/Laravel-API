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
    Schema::create('institutions', function (Blueprint $table) {
        $table->id('institution_id');
        $table->unsignedBigInteger('user_id')->unique();
        $table->string('name', 150);
        $table->text('address')->nullable();
        $table->text('description')->nullable();
        $table->string('website', 255)->nullable();
        $table->string('contact_person', 100)->nullable(); // جهة الاتصال
        $table->string('contact_phone', 20)->nullable();
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
        Schema::dropIfExists('institutions');
    }
};
