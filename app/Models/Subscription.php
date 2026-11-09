<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Subscription extends Model { protected $fillable = ['business_id','subscription_plan_id','provider','provider_subscription_id','status','trial_ends_at','current_period_ends_at','grace_ends_at']; protected function casts(): array { return ['trial_ends_at'=>'datetime','current_period_ends_at'=>'datetime','grace_ends_at'=>'datetime']; } public function business() { return $this->belongsTo(Business::class); } public function plan() { return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id'); } public function hasAccess(): bool { return in_array($this->status, ['active','trial','grace'], true) && (!$this->grace_ends_at || $this->grace_ends_at->isFuture()); } }
