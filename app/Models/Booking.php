<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Booking extends Model { protected $fillable = ['business_id','quote_id','enquiry_id','customer_id','service_id','assigned_user_id','starts_at','ends_at','status','recurrence','notes','completion_notes','estimated_total']; protected function casts(): array { return ['starts_at'=>'datetime','ends_at'=>'datetime','estimated_total'=>'decimal:2']; } public function business() { return $this->belongsTo(Business::class); } public function customer() { return $this->belongsTo(Customer::class); } public function service() { return $this->belongsTo(Service::class); } public function assignee() { return $this->belongsTo(User::class, 'assigned_user_id'); } public function quote() { return $this->belongsTo(Quote::class); } }
