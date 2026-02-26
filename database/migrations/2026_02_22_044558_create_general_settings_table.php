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
    Schema::create('general_settings', function (Blueprint $table) {
        $table->id('setting_id');
        $table->string('site_name', 100);
        $table->string('site_logo', 255)->nullable();
        $table->unsignedBigInteger('system_status')->nullable(); // حالة النظام من الـ lookup
        $table->string('content_email', 150)->unique();
        $table->string('content_phone', 20)->nullable();
        $table->text('privacy_policy')->nullable();
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // العلاقة
        $table->foreign('system_status')->references('value_id')->on('lookup_values')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
