<?php
namespace App\Http\Controllers;
use App\Models\SubscriptionPlan; use Illuminate\Http\Request; use Illuminate\Support\Facades\Mail;
class MarketingController extends Controller {
    public function pricing() { return view('marketing.pricing',['plans'=>SubscriptionPlan::where('active',true)->orderBy('id')->get()]); }
    public function contact() { return view('marketing.contact'); }
    public function sendContact(Request $request) { $data=$request->validate(['name'=>'required|string|max:120','email'=>'required|email|max:255','company'=>'nullable|string|max:160','message'=>'required|string|max:5000','website'=>'nullable|max:0']); Mail::raw("Name: {$data['name']}\nEmail: {$data['email']}\nCompany: ".($data['company']??'-')."\n\n{$data['message']}",fn($m)=>$m->to(config('cleanflow.sales_address'))->replyTo($data['email'],$data['name'])->subject('CleanFlow sales enquiry')); return back()->with('status','Thanks — your message has been sent to our sales team.'); }
}
