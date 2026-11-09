<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Quote;
use App\Models\QuoteEvent;
use App\Models\QuoteItem;
use App\Support\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function create(Request $request, Enquiry $enquiry) { $b=Workspace::business($request); Workspace::assertBusiness($enquiry,$b); return view('quotes.create',['enquiry'=>$enquiry->load('customer'),'business'=>$b,'services'=>$b->services()->where('active',true)->get()]); }
    public function store(Request $request, Enquiry $enquiry) {
        $b=Workspace::business($request); Workspace::assertBusiness($enquiry,$b); $data=$this->validateQuote($request); $subtotal=collect($data['items'])->sum(fn($i)=>(float)$i['quantity']*(float)$i['unit_price']); $tax=round($subtotal*((float)$data['tax_rate']/100),2);
        $quote=Quote::create(['business_id'=>$b->id,'enquiry_id'=>$enquiry->id,'customer_id'=>$enquiry->customer_id,'number'=>'CF-'.now()->format('ymd').'-'.str_pad((string)(Quote::count()+1),4,'0',STR_PAD_LEFT),'status'=>'draft','currency'=>$b->currency,'subtotal'=>$subtotal,'tax_rate'=>$data['tax_rate'],'tax_total'=>$tax,'total'=>$subtotal+$tax,'notes'=>$data['notes']??null,'terms'=>$data['terms']??$b->quote_terms,'expires_at'=>$data['expires_at']??now()->addDays($b->quote_expiry_days),'public_token'=>hash('sha256',Str::random(80)),'public_token_expires_at'=>now()->addDays($b->quote_expiry_days)]);
        foreach($data['items'] as $n=>$item) $quote->items()->create(['description'=>$item['description'],'quantity'=>$item['quantity'],'unit_price'=>$item['unit_price'],'total'=>$item['quantity']*$item['unit_price'],'sort_order'=>$n]);
        $this->event($quote,'created','Draft created by business.'); return redirect()->route('quotes.show',$quote)->with('status','Draft quote created. Review and approve it before sending.');
    }
    public function show(Request $request, Quote $quote) { $b=Workspace::business($request); Workspace::assertBusiness($quote,$b); return view('quotes.show',['quote'=>$quote->load(['items','customer','enquiry','events']),'business'=>$b]); }
    public function approve(Request $request, Quote $quote) { $b=Workspace::business($request); Workspace::assertBusiness($quote,$b); abort_unless($quote->status==='draft',422,'Only a draft can be approved.'); $quote->update(['status'=>'approved','approved_at'=>now(),'public_token_expires_at'=>now()->addDays($b->quote_expiry_days)]); $this->event($quote,'approved','Approved for customer delivery.'); return back()->with('status','Quote approved. It is ready to send.'); }
    public function send(Request $request, Quote $quote) { $b=Workspace::business($request); Workspace::assertBusiness($quote,$b); abort_unless($quote->status==='approved',422,'Approve this quote before sending.'); abort_unless($quote->customer->email,422,'This customer has no email address.'); $url=route('public.quote',$quote->public_token); $body=$b->quote_email_template ?: 'Hi {customer_name},\n\nYour quote {quote_number} from {business_name} is ready for review.'; $body=strtr($body,['{customer_name}'=>$quote->customer->name,'{quote_number}'=>$quote->number,'{business_name}'=>$b->name,'{quote_total}'=>$quote->currency.' '.number_format($quote->total,2),'{quote_url}'=>$url]); Mail::send('mail.quote',compact('quote','b','url','body'),function($message) use($quote,$b){$message->to($quote->customer->email,$quote->customer->name)->from(config('mail.from.address'),config('mail.from.name'))->subject("Your {$b->name} quote {$quote->number}");}); $quote->update(['status'=>'sent','sent_at'=>now(),'public_token_expires_at'=>now()->addDays($b->quote_expiry_days)]); $quote->enquiry?->update(['status'=>'quote_sent']); $this->event($quote,'sent','Quote email queued for delivery.'); return back()->with('status','Quote sent to '.$quote->customer->email.'.'); }
    public function printable(Request $request, Quote $quote) { $b=Workspace::business($request); Workspace::assertBusiness($quote,$b); return view('quotes.print',compact('quote','b')); }
    public function publicShow(string $token) { $quote=Quote::where('public_token',$token)->with(['items','customer','business'])->firstOrFail(); abort_unless($quote->isPubliclyViewable(),410,'This quote link has expired or is not available.'); return view('quotes.public',compact('quote')); }
    public function publicRespond(Request $request,string $token) { $quote=Quote::where('public_token',$token)->firstOrFail(); abort_unless($quote->isPubliclyViewable() && $quote->status==='sent',410,'This quote can no longer be changed.'); $data=$request->validate(['response'=>'required|in:accept,decline,changes','message'=>'nullable|string|max:2000']); $status=['accept'=>'accepted','decline'=>'declined','changes'=>'sent'][$data['response']]; $quote->update(['status'=>$status,'responded_at'=>now()]); if($status==='accepted') $quote->enquiry?->update(['status'=>'booked']); $this->event($quote,'customer_'.$data['response'],$data['message']??null); return redirect()->route('public.quote',$token)->with('status', $data['response']==='accept' ? 'Thank you — your acceptance has been recorded. The business will confirm the booking details.' : 'Your response has been recorded.'); }
    private function validateQuote(Request $request): array { return $request->validate(['items'=>'required|array|min:1','items.*.description'=>'required|string|max:255','items.*.quantity'=>'required|numeric|min:0.01|max:9999','items.*.unit_price'=>'required|numeric|min:0|max:999999','tax_rate'=>'required|numeric|min:0|max:100','notes'=>'nullable|string|max:5000','terms'=>'nullable|string|max:10000','expires_at'=>'nullable|date|after:today']); }
    private function event(Quote $quote,string $event,?string $message=null): void { QuoteEvent::create(['quote_id'=>$quote->id,'event'=>$event,'message'=>$message,'payload'=>['actor_user_id'=>auth()->id()]]); ActivityLog::create(['business_id'=>$quote->business_id,'user_id'=>auth()->id(),'subject_type'=>Quote::class,'subject_id'=>$quote->id,'action'=>'quote_'.$event]); }
}
