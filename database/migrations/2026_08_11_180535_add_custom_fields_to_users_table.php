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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->string('color')->nullable(); // لحفظ اللون المخصص للمستخدم
            $table->boolean('is_active')->default(true); // حالة المستخدم (نشط / موقوف)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'department',
                'job_title',
                'color',
                'is_active',
            ]);
        });
    }
};