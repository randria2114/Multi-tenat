<?php

namespace Database\Seeders;

use App\Domains\Module\Models\Module;
use App\Domains\Subscription\Models\Plan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Modules
        $smsModule = Module::create([
            'name' => 'SMS Gateway',
            'slug' => 'sms',
            'description' => 'Send SMS via custom API configurations.'
        ]);

        $paymentModule = Module::create([
            'name' => 'Payment Gateway',
            'slug' => 'payment',
            'description' => 'Accept payments using tenant-specific API keys.'
        ]);

        // 2. Create Plans
        $basicPlan = Plan::create([
            'name' => 'Basic Plan',
            'price' => 29.00,
            'max_users' => 5,
        ]);
        $basicPlan->modules()->attach($smsModule->id);

        $premiumPlan = Plan::create([
            'name' => 'Premium Plan',
            'price' => 99.00,
            'max_users' => 20,
        ]);
        $premiumPlan->modules()->attach([$smsModule->id, $paymentModule->id]);
    }
}
