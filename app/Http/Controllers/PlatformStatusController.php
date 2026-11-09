<?php
namespace App\Http\Controllers;
use App\Models\Business; use App\Models\Subscription; use App\Models\User;
class PlatformStatusController extends Controller { public function __invoke() { return view('platform.status',['businessCount'=>Business::count(),'userCount'=>User::count(),'activeSubscriptions'=>Subscription::where('status','active')->count(),'failedSubscriptions'=>Subscription::where('status','past_due')->count()]); } }
