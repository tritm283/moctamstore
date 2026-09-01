<?php
namespace App\Http\Controllers\Admin\V1;
use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class HomepageSectionController extends Controller
{
    private function rules():array{return ['title'=>['required','string','max:190'],'type'=>['required',Rule::in(['menu_bar','main_slider','category_grid','product_carousel','article_list','custom_html'])],'position'=>['required','integer','min:0'],'is_visible'=>['sometimes','boolean'],'settings'=>['nullable','array']];}
    public function index(){return $this->ok(HomepageSection::orderBy('position')->get());}
    public function store(Request $r){return $this->ok(HomepageSection::create($r->validate($this->rules())),'Đã tạo section.',201);}
    public function show(HomepageSection $homepageSection){return $this->ok($homepageSection);}
    public function update(Request $r,HomepageSection $homepageSection){$homepageSection->update($r->validate($this->rules()));return $this->ok($homepageSection->refresh(),'Đã cập nhật section.');}
    public function destroy(HomepageSection $homepageSection){$homepageSection->delete();return $this->ok(null,'Đã xóa section.');}
    public function reorder(Request $r){$items=$r->validate(['items'=>['required','array'],'items.*.section_id'=>['required','integer','exists:homepage_sections,section_id'],'items.*.position'=>['required','integer','min:0']])['items'];DB::transaction(fn()=>collect($items)->each(fn($x)=>HomepageSection::where('section_id',$x['section_id'])->update(['position'=>$x['position']])));return $this->ok(null,'Đã sắp xếp section.');}
}
