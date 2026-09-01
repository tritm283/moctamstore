<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\UserAddress;
use App\Services\AddressService;
use Illuminate\Http\Request;
class AddressController extends Controller
{
    public function index(Request $r){ return $this->ok(UserAddress::where('user_id',$r->user()->user_id)->orderByDesc('is_default')->orderByDesc('address_id')->get()); }
    public function store(AddressRequest $r,AddressService $s){ return $this->ok($s->create($r->user(),$r->validated()),'Đã thêm địa chỉ.',201); }
    public function update(AddressRequest $r,UserAddress $address,AddressService $s){ return $this->ok($s->update($r->user(),$address,$r->validated()),'Đã cập nhật địa chỉ.'); }
    public function destroy(Request $r,UserAddress $address){ abort_unless($address->user_id===$r->user()->user_id,404); if($address->is_default) abort(422,'Không thể xóa địa chỉ mặc định. Hãy chọn địa chỉ mặc định khác trước.'); $address->delete(); return $this->ok(null,'Đã xóa địa chỉ.'); }
    public function setDefault(Request $r,UserAddress $address,AddressService $s){ return $this->ok($s->setDefault($r->user(),$address),'Đã đặt làm mặc định.'); }
}
