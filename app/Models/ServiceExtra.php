<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ServiceExtra extends Model { protected $fillable = ['service_id','name','price','duration_minutes','active']; protected function casts(): array { return ['price'=>'decimal:2','active'=>'boolean']; } public function service() { return $this->belongsTo(Service::class); } }
