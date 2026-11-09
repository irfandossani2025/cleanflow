<?php
namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function login(Request $request) { $credentials=$request->validate(['email'=>['required','email'],'password'=>['required','string']]); if (!Auth::attempt($credentials, $request->boolean('remember'))) return back()->withErrors(['email'=>'These credentials do not match our records.'])->onlyInput('email'); $request->session()->regenerate(); return redirect()->intended(route('dashboard')); }
    public function showRegister() { return view('auth.register'); }
    public function register(Request $request) {
        $data=$request->validate(['name'=>['required','string','max:120'],'business_name'=>['required','string','max:160'],'email'=>['required','email','max:255','unique:users'],'password'=>['required','confirmed','min:10']]);
        $business=Business::create(['name'=>$data['business_name'],'slug'=>Str::slug($data['business_name']).'-'.Str::lower(Str::random(6)),'email'=>$data['email']]);
        $user=User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>$data['password'],'business_id'=>$business->id,'role'=>'owner']); event(new Registered($user)); Auth::login($user); return redirect()->route('onboarding.show');
    }
    public function logout(Request $request) { Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('home'); }
    public function verifyNotice() { return view('auth.verify-email'); }
    public function resendVerification(Request $request) { if ($request->user()->hasVerifiedEmail()) return redirect()->route('dashboard'); $request->user()->sendEmailVerificationNotification(); return back()->with('status','A new verification link has been sent.'); }
    public function showForgot() { return view('auth.forgot-password'); }
    public function sendReset(Request $request) { $request->validate(['email'=>['required','email']]); $status=Password::sendResetLink($request->only('email')); return back()->with($status===Password::RESET_LINK_SENT?'status':'error', __($status)); }
    public function showReset(Request $request, string $token) { return view('auth.reset-password',['token'=>$token,'email'=>$request->query('email')]); }
    public function reset(Request $request) { $request->validate(['token'=>'required','email'=>'required|email','password'=>'required|confirmed|min:10']); $status=Password::reset($request->only('email','password','password_confirmation','token'), function (User $user, string $password) { $user->forceFill(['password'=>Hash::make($password),'remember_token'=>Str::random(60)])->save(); }); return $status===Password::PASSWORD_RESET ? redirect()->route('login')->with('status',__($status)) : back()->withErrors(['email'=>__($status)]); }
}
