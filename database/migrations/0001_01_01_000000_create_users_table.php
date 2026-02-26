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
    Schema::create('users', function (Blueprint $table) {
        $table->id('user_id'); // معرف المستخدم
        $table->string('full_name', 150); // الاسم الكامل
        $table->string('email', 150)->unique(); // البريد الإلكتروني
        $table->string('password'); // كلمة المرور المشفرة (password_hash)
        $table->string('phone', 20)->nullable(); // رقم الهاتف
        $table->enum('user_type', ['student', 'institution', 'supervisor', 'admin']); // نوع المستخدم
        $table->string('profile_picture', 255)->nullable(); // صورة الملف الشخصي
        $table->enum('status', ['active', 'inactive', 'suspended'])->default('active'); // حالة الحساب
        $table->timestamp('email_verified_at')->nullable(); 
        $table->timestamp('last_login_at')->nullable();
        $table->boolean('is_active')->default(true); // مفعل / معطل
        $table->unsignedBigInteger('created_by')->nullable(); // منشئ السجل
        $table->unsignedBigInteger('updated_by')->nullable(); // معدل السجل
        $table->timestamps(); // تُنشئ created_at و updated_at تلقائياً
    });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
