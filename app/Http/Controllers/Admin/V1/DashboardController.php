<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
class DashboardController extends Controller
{
    public function index(){return $this->ok(['users'=>User::count(),'products'=>Product::count(),'articles'=>Article::count(),'orders'=>Order::count(),'pending_orders'=>Order::where('status','pending')->count(),'revenue_completed'=>(string)Order::where('status','completed')->sum('total_amount')]);}
}
