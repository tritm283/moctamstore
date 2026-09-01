export interface ApiResponse<T> { message: string; data: T }
export interface Paginated<T> {
  current_page: number; data: T[]; first_page_url?: string; from: number | null; last_page: number;
  last_page_url?: string; links?: unknown[]; next_page_url?: string | null; path?: string; per_page: number;
  prev_page_url?: string | null; to: number | null; total: number;
}
export interface Media { media_id: number; drive_file_id: string; drive_view_url: string; file_name: string; mime_type?: string | null; size_bytes?: number | null }
export interface User { user_id:number; email:string; phone?:string|null; full_name:string; role:string; is_active:boolean; is_marketing_allowed:boolean; avatar?:Media|null; addresses?:Address[]; social_accounts?:SocialAccount[]; created_at?:string }
export interface Address { address_id:number; receiver_name:string; receiver_phone:string; province_city:string; district:string; ward_commune:string; detailed_address:string; is_default:boolean }
export interface SocialAccount { social_account_id:number; provider:string; provider_user_id:string }
export interface Category { category_id:number; name:string; slug:string; description?:string|null; parent_id?:number|null; is_active:boolean; position:number }
export interface Product { product_id:number; category_id:number; media_id?:number|null; name:string; slug:string; sku:string; short_description?:string|null; description?:string|null; price:string|number; stock_quantity:number; is_active:boolean; media?:Media|null; category?:Category|null; created_at?:string }
export interface ArticleCategory { article_category_id:number; name:string; slug:string; description?:string|null; is_active:boolean }
export interface Article { article_id:number; article_category_id:number; thumbnail_id?:number|null; title:string; slug:string; excerpt?:string|null; content:string; status:'draft'|'published'|'archived'; published_at?:string|null; thumbnail?:Media|null; category?:ArticleCategory|null; created_at?:string }
export interface Payment { payment_id:number; order_id:number; method:string; status:'pending'|'success'|'failed'; amount:string|number; transaction_code?:string|null; gateway_payload?:Record<string,unknown>|null; paid_at?:string|null; created_at?:string }
export interface OrderItem { order_item_id:number; product_id:number; product_name:string; quantity:number; price:string|number }
export interface Order { order_id:number; user_id:number; status:'pending'|'confirmed'|'processing'|'shipping'|'completed'|'cancelled'; total_amount:string|number; shipping_address:Record<string, any>; note?:string|null; payment?:Payment|null; items?:OrderItem[]; created_at?:string }
export interface HomepageSection { section_id:number; title:string; type:'menu_bar'|'main_slider'|'category_grid'|'product_carousel'|'article_list'|'custom_html'; position:number; is_visible:boolean; settings?:Record<string,unknown>|null }
export interface Menu { menu_id:number; title:string; parent_id?:number|null; url?:string|null; resolved_url?:string|null; position:number; is_visible:boolean; category_id?:number|null; product_id?:number|null; article_category_id?:number|null; article_id?:number|null; children?:Menu[] }
export interface DashboardStats { users:number; products:number; articles:number; orders:number; pending_orders:number; revenue_completed:string|number }
