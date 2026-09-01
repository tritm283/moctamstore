<?php
namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\HomepageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => ['required','string','max:190'],
            'parent_id' => ['nullable','integer','exists:menus,menu_id'],
            'url' => ['nullable','string','max:1000'],
            'position' => ['required','integer','min:0'],
            'is_visible' => ['sometimes','boolean'],
            'category_id' => ['nullable','integer','exists:categories,category_id'],
            'product_id' => ['nullable','integer','exists:products,product_id'],
            'article_category_id' => ['nullable','integer','exists:article_categories,article_category_id'],
            'article_id' => ['nullable','integer','exists:articles,article_id'],
        ];
    }

    private function validateTarget(array $d): void
    {
        $n = collect(['category_id','product_id','article_category_id','article_id'])
            ->filter(fn ($k) => !empty($d[$k]))->count();
        if ($n > 1) abort(422, 'Menu chỉ được trỏ tới tối đa 1 loại nội dung.');
        if ($n === 0 && empty($d['url'])) abort(422, 'Custom Link bắt buộc có url.');
    }

    private function assertNoCycle(Menu $menu, ?int $parentId): void
    {
        if ($parentId === null) return;
        if ($parentId === $menu->menu_id) abort(422, 'Menu không thể làm cha của chính nó.');

        $cursor = $parentId;
        $guard = 0;
        while ($cursor !== null && $guard++ < 1000) {
            if ($cursor === $menu->menu_id) abort(422, 'Cấu trúc menu sẽ tạo vòng lặp đệ quy.');
            $cursor = Menu::where('menu_id', $cursor)->value('parent_id');
        }
        if ($guard >= 1000) abort(422, 'Cây menu không hợp lệ.');
    }

    public function index(HomepageService $s)
    {
        return $this->ok(['tree' => $s->menuTree(false), 'flat' => $s->menuFlat(false)]);
    }

    public function store(Request $r)
    {
        $d = $r->validate($this->rules());
        $this->validateTarget($d);
        return $this->ok(Menu::create($d), 'Đã tạo menu.', 201);
    }

    public function show(Menu $menu) { return $this->ok($menu); }

    public function update(Request $r, Menu $menu)
    {
        $d = $r->validate($this->rules());
        $this->validateTarget($d);
        $this->assertNoCycle($menu, $d['parent_id'] ?? null);
        $menu->update($d);
        return $this->ok($menu->refresh(), 'Đã cập nhật menu.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return $this->ok(null, 'Đã xóa menu.');
    }

    public function reorder(Request $r)
    {
        $items = $r->validate([
            'items' => ['required','array'],
            'items.*.menu_id' => ['required','integer','exists:menus,menu_id'],
            'items.*.parent_id' => ['nullable','integer','exists:menus,menu_id'],
            'items.*.position' => ['required','integer','min:0'],
        ])['items'];

        DB::transaction(function () use ($items) {
            foreach ($items as $x) {
                $menu = Menu::where('menu_id', $x['menu_id'])->lockForUpdate()->firstOrFail();
                $this->assertNoCycle($menu, $x['parent_id'] ?? null);
                $menu->update(['parent_id' => $x['parent_id'] ?? null, 'position' => $x['position']]);
            }
        });
        return $this->ok(null, 'Đã sắp xếp menu.');
    }
}
