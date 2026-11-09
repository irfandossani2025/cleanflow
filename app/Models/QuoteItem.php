<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class QuoteItem extends Model { protected $fillable = ['quote_id','description','quantity','unit_price','total','sort_order']; protected function casts(): array { return ['quantity'=>'decimal:2','unit_price'=>'decimal:2','total'=>'decimal:2']; } public function quote() { return $this->belongsTo(Quote::class); } }
