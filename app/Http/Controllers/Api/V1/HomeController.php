<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Services\HomepageService;
class HomeController extends Controller
{
    public function index(HomepageService $service){ return $this->ok(['menus'=>$service->menuTree(),'sections'=>$service->sections()]); }
    public function menus(HomepageService $service){ return $this->ok($service->menuTree()); }
}
