<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SubscriptionPlan extends Model
{
    protected $fillable = [
        'key', 'name', 'currency', 'monthly_price', 'price', 'billing_interval',
        'billing_interval_count', 'annual_discount_percent', 'enquiry_limit', 'staff_limit',
        'trial_days', 'paypal_plan_id', 'active', 'features',
    ];

    protected function casts(): array
    {
        return [
            'monthly_price' => 'decimal:2',
            'price' => 'decimal:2',
            'annual_discount_percent' => 'decimal:2',
            'active' => 'boolean',
            'features' => 'array',
        ];
    }
}
