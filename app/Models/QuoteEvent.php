<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class QuoteEvent extends Model { public $timestamps = false; protected $fillable = ['quote_id','event','message','payload','occurred_at']; protected function casts(): array { return ['payload'=>'array','occurred_at'=>'datetime']; } public function quote() { return $this->belongsTo(Quote::class); } }
