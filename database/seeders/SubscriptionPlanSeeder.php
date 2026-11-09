<?php
namespace Database\Seeders;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
class SubscriptionPlanSeeder extends Seeder {
    public function run(): void {
        foreach ([
            ['key'=>'starter','name'=>'Starter','currency'=>'USD','monthly_price'=>null,'enquiry_limit'=>null,'staff_limit'=>2,'trial_days'=>0,'features'=>['Core enquiries','Quotes','Bookings']],
            ['key'=>'growth','name'=>'Growth','currency'=>'USD','monthly_price'=>null,'enquiry_limit'=>null,'staff_limit'=>10,'trial_days'=>0,'features'=>['Everything in Starter','Follow-up reminders','Team scheduling']],
        ] as $plan) SubscriptionPlan::updateOrCreate(['key'=>$plan['key']], $plan);
    }
}
