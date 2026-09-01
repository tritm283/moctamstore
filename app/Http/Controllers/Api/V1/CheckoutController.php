<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request,CheckoutService $service){ $d=$request->validated(); $order=$service->checkout($request->user(),$d['address_id'],$d['payment_method'],$d['note']??null); return $this->ok($order,'Đặt hàng thành công.',201); }
}
