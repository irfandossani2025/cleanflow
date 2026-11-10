<?php
namespace Database\Seeders;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
class SubscriptionPlanSeeder extends Seeder {
    public function run(): void {
        SubscriptionPlan::whereIn('key', ['starter', 'growth'])->update(['active' => false]);

        foreach ([
            [
                'key' => 'cleanflow-monthly', 'name' => 'CleanFlow Monthly', 'currency' => 'USD',
                'monthly_price' => 15, 'price' => 15, 'billing_interval' => 'month',
                'billing_interval_count' => 1, 'annual_discount_percent' => null,
                'enquiry_limit' => null, 'staff_limit' => null, 'trial_days' => 0,
                'active' => true,
                'features' => ['Enquiries and customer history', 'Reviewed quotes and secure approvals', 'Email follow-ups', 'Bookings and staff scheduling'],
            ],
            [
                'key' => 'cleanflow-yearly', 'name' => 'CleanFlow Yearly', 'currency' => 'USD',
                'monthly_price' => null, 'price' => 144, 'billing_interval' => 'year',
                'billing_interval_count' => 1, 'annual_discount_percent' => 20,
                'enquiry_limit' => null, 'staff_limit' => null, 'trial_days' => 0,
                'active' => true,
                'features' => ['Everything in CleanFlow Monthly', 'US$36 annual saving', 'Same core features for the full year', 'Priority launch updates'],
            ],
        ] as $plan) {
            SubscriptionPlan::updateOrCreate(['key' => $plan['key']], $plan);
        }
    }
}
