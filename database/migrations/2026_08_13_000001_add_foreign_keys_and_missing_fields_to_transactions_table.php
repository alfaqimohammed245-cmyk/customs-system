<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'trader_id')) {
                $table->foreignId('trader_id')->nullable()->constrained('traders')->nullOnDelete();
            }
            if (!Schema::hasColumn('transactions', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            }
            if (!Schema::hasColumn('transactions', 'status_id')) {
                $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            }
            if (!Schema::hasColumn('transactions', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transactions', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transactions', 'attachment')) {
                $table->string('attachment')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'sabir_status')) {
                $table->string('sabir_status')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'waybill_number')) {
                $table->string('waybill_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // لا نحذف أي حقول للحفاظ على سلامة البيانات
        });
    }
};
