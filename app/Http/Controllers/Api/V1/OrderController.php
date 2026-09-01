<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    public function index(Request $r){ return $this->ok(Order::with('payment')->where('user_id',$r->user()->user_id)->orderByDesc('order_id')->paginate(20)); }
    public function show(Request $r,Order $order){ abort_unless($order->user_id===$r->user()->user_id,404); return $this->ok($order->load(['items','payment'])); }
}
