<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SubscriptionPlan extends Model { protected $fillable = ['key','name','currency','monthly_price','enquiry_limit','staff_limit','trial_days','paypal_plan_id','active','features']; protected function casts(): array { return ['monthly_price'=>'decimal:2','active'=>'boolean','features'=>'array']; } }
