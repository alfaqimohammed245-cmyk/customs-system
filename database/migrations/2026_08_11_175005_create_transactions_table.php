<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // حقول العملية الأساسية
            $table->string('transaction_number')->unique(); // رقم العملية
            $table->string('trader_name'); // اسم التاجر
            $table->integer('packages_count')->default(0); // عدد الطرود
            $table->string('policy_number')->nullable(); // رقم البوليصة
            $table->text('container_numbers')->nullable(); // أرقام الحاويات
            $table->date('receipt_date')->nullable(); // تاريخ الاستلام
            $table->string('sabir')->nullable(); // سابر
            $table->text('documents')->nullable(); // المستندات
            $table->text('receipt_notes')->nullable(); // ملاحظات الاستلام
            $table->boolean('received_policy')->default(false); // استلام البوليصة
            $table->date('policy_receipt_date')->nullable(); // تاريخ استلام البوليصة
            $table->string('company_name')->nullable(); // الشركة

            // الفواتير والإذن
            $table->decimal('delivery_order_invoice', 10, 2)->nullable(); // فاتورة إذن التسليم
            $table->text('delivery_order_notes')->nullable(); // ملاحظات إذن التسليم
            $table->string('declaration_number')->nullable(); // رقم البيان
            $table->date('declaration_date')->nullable(); // تاريخ البيان
            $table->date('unloading_date')->nullable(); // تاريخ التفريغ
            $table->string('declaration_status')->nullable(); // حالة البيان
            $table->string('container_location')->nullable(); // موقع الحاوية
            $table->text('container_notes')->nullable(); // ملاحظات الحاوية
            $table->decimal('ports_invoice', 10, 2)->nullable(); // فاتورة الموانئ
            $table->decimal('operator_invoice', 10, 2)->nullable(); // فاتورة المشغل

            // التحميل وترجيع الفاضي
            $table->string('loading')->nullable(); // التحميل
            $table->string('driver')->nullable(); // السائق
            $table->date('empty_return_date')->nullable(); // ترجيع الفاضي
            $table->text('empty_return_notes')->nullable(); // ملاحظات ترجيع الفاضي
            $table->decimal('client_invoice', 10, 2)->nullable(); // فاتورة العميل

            // حقول النظام
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // الموظف المسؤول
            $table->string('status')->default('جديدة'); // حالة العملية
            $table->integer('progress_percentage')->default(0); // نسبة الإنجاز

            $table->timestamps();
        }); // <-- إغلاق جدول transactions
    } // <-- إغلاق دالة up

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
