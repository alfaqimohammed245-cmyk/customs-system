<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء حساب مدير النظام الأساسي
        $admin = User::create([
            'name' => 'مدير النظام',
            'username' => 'admin',
            'email' => 'abnmaak23@gmail.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // 2. إنشاء عملية تجريبية أولى مرتبطة بالمدير
        Transaction::create([
            'transaction_number' => 'TRX-2026-001',
            'trader_name' => 'شركة التجارة السريعة',
            'packages_count' => 15,
            'policy_number' => 'POL-987654',
            'user_id' => $admin->id,
            'status' => 'جديدة',
            'progress_percentage' => 10,
        ]);
    }
}
