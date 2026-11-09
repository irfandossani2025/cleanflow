<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Business extends Model {
    protected $fillable = ['name','slug','email','phone','address','country','timezone','locale','currency','logo_path','service_area','quote_terms','quote_expiry_days','working_hours','quote_email_template','onboarding_completed'];
    protected function casts(): array { return ['working_hours' => 'array','onboarding_completed' => 'boolean']; }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function services(): HasMany { return $this->hasMany(Service::class); }
    public function enquiries(): HasMany { return $this->hasMany(Enquiry::class); }
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
    public function quotes(): HasMany { return $this->hasMany(Quote::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
    public function subscription() { return $this->hasOne(Subscription::class); }
}
