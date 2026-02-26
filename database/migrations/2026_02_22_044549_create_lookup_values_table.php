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
    Schema::create('lookup_values', function (Blueprint $table) {
        $table->id('value_id');
        $table->unsignedBigInteger('lookup_id');
        $table->string('value_code', 50); // مثل ACTIVE
        $table->text('description')->nullable();
        $table->text('value_data')->nullable(); // بيانات إضافية للقيمة
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // العلاقة
        $table->foreign('lookup_id')->references('lookup_id')->on('lookup_masters')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lookup_values');
    }
};
