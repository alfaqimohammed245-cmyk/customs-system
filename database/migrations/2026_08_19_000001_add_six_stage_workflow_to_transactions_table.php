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
        Schema::table('transactions', function (Blueprint $table) {
            // المرحلة الحالية (من 1 إلى 6)
            if (!Schema::hasColumn('transactions', 'current_stage')) {
                $table->unsignedTinyInteger('current_stage')->default(1);
            }

            // الوكيل الملاحي (Shipping Agent) لربط البوليصة
            if (!Schema::hasColumn('transactions', 'shipping_agent')) {
                $table->string('shipping_agent')->nullable();
            }

            // المرحلة 1: الاستلام والترقيم
            if (!Schema::hasColumn('transactions', 'stage_1_completed')) {
                $table->boolean('stage_1_completed')->default(false);
            }
            if (!Schema::hasColumn('transactions', 'stage_1_notes')) {
                $table->text('stage_1_notes')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_1_delay_reason')) {
                $table->text('stage_1_delay_reason')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_1_completed_at')) {
                $table->dateTime('stage_1_completed_at')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_1_user_id')) {
                $table->foreignId('stage_1_user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            // المرحلة 2: ربط البوليصة بالبيان (الوكيل الملاحي)
            if (!Schema::hasColumn('transactions', 'stage_2_completed')) {
                $table->boolean('stage_2_completed')->default(false);
            }
            if (!Schema::hasColumn('transactions', 'stage_2_notes')) {
                $table->text('stage_2_notes')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_2_delay_reason')) {
                $table->text('stage_2_delay_reason')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_2_completed_at')) {
                $table->dateTime('stage_2_completed_at')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_2_user_id')) {
                $table->foreignId('stage_2_user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            // المرحلة 3: التخليص والإجراءات الجمركية (الميناء والمعاينة والرسوم)
            if (!Schema::hasColumn('transactions', 'stage_3_completed')) {
                $table->boolean('stage_3_completed')->default(false);
            }
            if (!Schema::hasColumn('transactions', 'stage_3_notes')) {
                $table->text('stage_3_notes')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_3_delay_reason')) {
                $table->text('stage_3_delay_reason')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_3_completed_at')) {
                $table->dateTime('stage_3_completed_at')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_3_user_id')) {
                $table->foreignId('stage_3_user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            // المرحلة 4: خروج الحاويات والنقل (الفسح)
            if (!Schema::hasColumn('transactions', 'stage_4_completed')) {
                $table->boolean('stage_4_completed')->default(false);
            }
            if (!Schema::hasColumn('transactions', 'stage_4_notes')) {
                $table->text('stage_4_notes')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_4_delay_reason')) {
                $table->text('stage_4_delay_reason')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_4_completed_at')) {
                $table->dateTime('stage_4_completed_at')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_4_user_id')) {
                $table->foreignId('stage_4_user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            // المرحلة 5: إعادة الحاويات الفارغة (ترجيع الفاضي)
            if (!Schema::hasColumn('transactions', 'stage_5_completed')) {
                $table->boolean('stage_5_completed')->default(false);
            }
            if (!Schema::hasColumn('transactions', 'stage_5_notes')) {
                $table->text('stage_5_notes')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_5_delay_reason')) {
                $table->text('stage_5_delay_reason')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_5_completed_at')) {
                $table->dateTime('stage_5_completed_at')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_5_user_id')) {
                $table->foreignId('stage_5_user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            // المرحلة 6: الإنجاز والفاتورة والمبيعات (الإغلاق النهائي)
            if (!Schema::hasColumn('transactions', 'stage_6_completed')) {
                $table->boolean('stage_6_completed')->default(false);
            }
            if (!Schema::hasColumn('transactions', 'stage_6_notes')) {
                $table->text('stage_6_notes')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_6_delay_reason')) {
                $table->text('stage_6_delay_reason')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_6_completed_at')) {
                $table->dateTime('stage_6_completed_at')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'stage_6_user_id')) {
                $table->foreignId('stage_6_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // الحفاظ على سلامة البيانات
        });
    }
};
