<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model { protected $fillable = ['business_id','name','description','duration_minutes','base_price','active']; protected function casts(): array { return ['base_price'=>'decimal:2','active'=>'boolean']; } public function business() { return $this->belongsTo(Business::class); } public function extras() { return $this->hasMany(ServiceExtra::class); } }
