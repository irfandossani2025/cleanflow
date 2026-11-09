<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Enquiry extends Model { protected $fillable = ['business_id','customer_id','service_id','status','source','property_type','property_address','bedrooms','bathrooms','preferred_date','notes','received_at','follow_up_at']; protected function casts(): array { return ['received_at'=>'datetime','follow_up_at'=>'datetime','preferred_date'=>'date']; } public function business() { return $this->belongsTo(Business::class); } public function customer() { return $this->belongsTo(Customer::class); } public function service() { return $this->belongsTo(Service::class); } public function quotes() { return $this->hasMany(Quote::class); } }
