<?php
namespace Tests\Feature;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class MenuConstraintTest extends TestCase
{
    use RefreshDatabase;
    public function test_menu_accepts_only_one_polymorphic_target(): void
    {
        $category=Category::create(['name'=>'Nam','slug'=>'nam','position'=>0]);
        $articleCategory=ArticleCategory::create(['name'=>'Tin','slug'=>'tin']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        Menu::create(['title'=>'Invalid','position'=>0,'category_id'=>$category->category_id,'article_category_id'=>$articleCategory->article_category_id]);
    }
}
