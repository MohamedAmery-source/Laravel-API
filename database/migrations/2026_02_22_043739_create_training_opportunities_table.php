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
    Schema::create('training_opportunities', function (Blueprint $table) {
        $table->id('opportunity_id');
        $table->unsignedBigInteger('institution_id'); // المؤسسة التابعة لها
        $table->string('title', 200);
        $table->text('description')->nullable();
        $table->text('required_skills')->nullable();
        $table->integer('available_seats')->default(1);
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->date('application_deadline')->nullable();
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // العلاقة
        $table->foreign('institution_id')->references('institution_id')->on('institutions')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_opportunities');
    }
};
