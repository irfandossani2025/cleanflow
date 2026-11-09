<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model { protected $fillable = ['business_id','name','email','phone','address','marketing_opt_out']; protected function casts(): array { return ['marketing_opt_out'=>'boolean']; } public function business() { return $this->belongsTo(Business::class); } public function enquiries() { return $this->hasMany(Enquiry::class); } public function quotes() { return $this->hasMany(Quote::class); } }
