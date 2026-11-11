<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoController extends Controller
{
    public function __invoke(Request $request)
    {
        $business = Business::firstOrCreate(
            ['slug' => 'cleanflow-demo'],
            [
                'name' => 'Harbor & Home Cleaning',
                'email' => 'hello@harborhome.example',
                'country' => 'US',
                'timezone' => 'America/New_York',
                'locale' => 'en',
                'currency' => 'USD',
                'service_area' => 'Harbor City and nearby neighborhoods',
                'quote_terms' => 'Demo terms only — no real service is offered from this workspace.',
                'onboarding_completed' => true,
            ],
        );

        $user = User::firstOrCreate(
            ['email' => 'demo@cleanflow.invalid'],
            [
                'name' => 'CleanFlow Demo',
                'business_id' => $business->id,
                'role' => 'owner',
                'password' => Hash::make(Str::random(48)),
            ],
        );
        if (!$user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        if (!$business->customers()->exists()) {
            $this->seedWorkspace($business, $user);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'You are exploring a read-only demo workspace. No real customer data is shown and changes are disabled.');
    }

    private function seedWorkspace(Business $business, User $owner): void
    {
        $standard = Service::create(['business_id' => $business->id, 'name' => 'Standard home clean', 'description' => 'A regular refresh for lived-in homes.', 'duration_minutes' => 180, 'base_price' => 165, 'active' => true]);
        $deep = Service::create(['business_id' => $business->id, 'name' => 'Deep clean', 'description' => 'A detailed top-to-bottom clean.', 'duration_minutes' => 300, 'base_price' => 285, 'active' => true]);
        $moveOut = Service::create(['business_id' => $business->id, 'name' => 'Move-out clean', 'description' => 'A fresh handover-ready clean.', 'duration_minutes' => 360, 'base_price' => 340, 'active' => true]);

        $mia = Customer::create(['business_id' => $business->id, 'name' => 'Mia Carter', 'email' => 'mia.carter@example.test', 'phone' => '+1 555 010 0142', 'address' => '18 Harbor Lane, Harbor City']);
        $noah = Customer::create(['business_id' => $business->id, 'name' => 'Noah Williams', 'email' => 'noah.williams@example.test', 'phone' => '+1 555 010 0189', 'address' => '6 Cedar Street, Harbor City']);
        $ava = Customer::create(['business_id' => $business->id, 'name' => 'Ava Patel', 'email' => 'ava.patel@example.test', 'phone' => '+1 555 010 0113', 'address' => '40 Maple Court, Harbor City']);

        $new = Enquiry::create(['business_id' => $business->id, 'customer_id' => $mia->id, 'service_id' => $deep->id, 'status' => 'new', 'source' => 'website', 'property_type' => 'Townhouse', 'property_address' => $mia->address, 'bedrooms' => 3, 'bathrooms' => 2, 'preferred_date' => now()->addDays(5)->toDateString(), 'notes' => 'Interested in a first deep clean followed by regular visits.', 'received_at' => now()->subHours(3), 'follow_up_at' => now()->addDay()]);
        $sent = Enquiry::create(['business_id' => $business->id, 'customer_id' => $noah->id, 'service_id' => $moveOut->id, 'status' => 'quote_sent', 'source' => 'referral', 'property_type' => 'Apartment', 'property_address' => $noah->address, 'bedrooms' => 2, 'bathrooms' => 2, 'preferred_date' => now()->addDays(8)->toDateString(), 'notes' => 'End-of-lease clean needed before key handover.', 'received_at' => now()->subDays(2), 'follow_up_at' => now()->addDay()]);
        $booked = Enquiry::create(['business_id' => $business->id, 'customer_id' => $ava->id, 'service_id' => $standard->id, 'status' => 'booked', 'source' => 'repeat_customer', 'property_type' => 'House', 'property_address' => $ava->address, 'bedrooms' => 4, 'bathrooms' => 3, 'preferred_date' => now()->addDays(3)->toDateString(), 'notes' => 'Monthly maintenance clean.', 'received_at' => now()->subDays(9)]);

        $quote = Quote::create(['business_id' => $business->id, 'enquiry_id' => $sent->id, 'customer_id' => $noah->id, 'number' => 'DEMO-1002', 'status' => 'sent', 'currency' => 'USD', 'subtotal' => 340, 'tax_rate' => 0, 'tax_total' => 0, 'total' => 340, 'notes' => 'Demo quote awaiting a customer decision.', 'terms' => $business->quote_terms, 'expires_at' => now()->addDays(7), 'sent_at' => now()->subDay(), 'public_token' => Str::random(48), 'public_token_expires_at' => now()->addDays(7)]);
        QuoteItem::create(['quote_id' => $quote->id, 'description' => 'Move-out clean', 'quantity' => 1, 'unit_price' => 340, 'total' => 340, 'sort_order' => 1]);

        Booking::create(['business_id' => $business->id, 'enquiry_id' => $booked->id, 'customer_id' => $ava->id, 'service_id' => $standard->id, 'assigned_user_id' => $owner->id, 'starts_at' => now()->addDays(3)->setTime(9, 0), 'ends_at' => now()->addDays(3)->setTime(12, 0), 'status' => 'scheduled', 'recurrence' => 'monthly', 'notes' => 'Use the side entrance. Pets will be away.', 'estimated_total' => 165]);
        Booking::create(['business_id' => $business->id, 'customer_id' => $mia->id, 'service_id' => $deep->id, 'assigned_user_id' => $owner->id, 'starts_at' => now()->addDays(6)->setTime(10, 0), 'ends_at' => now()->addDays(6)->setTime(15, 0), 'status' => 'scheduled', 'recurrence' => 'once', 'notes' => 'Demo upcoming job.', 'estimated_total' => 285]);
    }
}
