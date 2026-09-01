<?php
namespace App\Services;
use App\Models\HomepageSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
class HomepageService
{
    public function sections(): Collection { return HomepageSection::where('is_visible',true)->orderBy('position')->orderBy('section_id')->get(); }
    public function menuFlat(bool $visibleOnly=true): Collection
    {
        $query=DB::table('menus as m')
            ->leftJoin('categories as c','c.category_id','=','m.category_id')
            ->leftJoin('products as p','p.product_id','=','m.product_id')
            ->leftJoin('article_categories as ac','ac.article_category_id','=','m.article_category_id')
            ->leftJoin('articles as a','a.article_id','=','m.article_id')
            ->select(['m.menu_id','m.title','m.parent_id','m.position','m.is_visible','m.url','m.category_id','m.product_id','m.article_category_id','m.article_id'])
            ->selectRaw("CASE WHEN m.category_id IS NOT NULL THEN '/danh-muc/' || c.category_id::text WHEN m.product_id IS NOT NULL THEN '/san-pham/' || p.product_id::text WHEN m.article_category_id IS NOT NULL THEN '/chuyen-muc/' || ac.slug WHEN m.article_id IS NOT NULL THEN '/bai-viet/' || a.slug ELSE m.url END AS resolved_url")
            ->orderBy('m.position')->orderBy('m.menu_id');
        if ($visibleOnly) $query->where('m.is_visible',true);
        return $query->get();
    }
    public function menuTree(bool $visibleOnly=true): array
    {
        $rows=$this->menuFlat($visibleOnly)->map(fn($x)=>(array)$x)->all(); $byParent=[];
        foreach($rows as $row) $byParent[$row['parent_id']??0][]=$row;
        $build=function($parent=0) use (&$build,&$byParent){ return array_map(function($row) use (&$build){ $row['children']=$build($row['menu_id']); return $row; },$byParent[$parent]??[]); };
        return $build(0);
    }
}
