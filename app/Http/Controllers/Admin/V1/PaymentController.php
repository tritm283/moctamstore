<?php
namespace App\Http\Controllers\Admin\V1;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class PaymentController extends Controller
{
    public function index(Request $r){$q=Payment::query();if($r->filled('status'))$q->where('status',$r->status);return $this->ok($q->orderByDesc('payment_id')->paginate(30));}
    public function show(Payment $payment){return $this->ok($payment);}
    public function status(Request $r,Payment $payment){$d=$r->validate(['status'=>['required',Rule::enum(PaymentStatus::class)],'transaction_code'=>['nullable','string','max:190'],'gateway_payload'=>['nullable','array']]);if($d['status']===PaymentStatus::SUCCESS->value)$d['paid_at']=now();$payment->update($d);return $this->ok($payment->refresh(),'Đã cập nhật thanh toán.');}
}
