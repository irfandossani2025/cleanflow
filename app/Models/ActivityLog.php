<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityLog extends Model { protected $fillable = ['business_id','user_id','subject_type','subject_id','action','properties']; protected function casts(): array { return ['properties'=>'array']; } public function subject() { return $this->morphTo(); } }
