# Ecommerce + CMS API — Laravel 13 / PostgreSQL

Backend dùng chung cho:
- Storefront: NuxtJS → `/api/v1/*`
- Admin SPA: VueJS → `/api/admin/v1/*`

## Kiến trúc
- Laravel 13, PHP 8.3+
- PostgreSQL
- Laravel Sanctum token auth
- Google Drive API lưu ảnh tập trung vào bảng `media`
- Service layer cho nghiệp vụ: upload, social auth, cart, checkout, homepage/menu
- DB transaction + `FOR UPDATE` khi checkout để khóa giỏ/sản phẩm và tránh oversell
- Public API và Admin API tách route/controller; cùng dùng service/model để tránh lặp logic

## Cài đặt
```bash
composer install
cp .env.example .env
php artisan key:generate
# chỉnh DB_* và Google/Facebook trong .env
php artisan migrate --seed
php artisan serve
```

Mặc định seeder dùng:
- `ADMIN_EMAIL=admin@example.com`
- `ADMIN_PASSWORD=ChangeMe123!`

Hãy đặt 2 biến này trong `.env` trước khi seed ở production.

## Google Drive
1. Tạo Google Cloud project, bật Drive API.
2. Tạo Service Account và tải JSON key.
3. Nếu upload vào thư mục Drive cụ thể, chia sẻ folder đó cho email Service Account với quyền Editor.
4. Đặt `GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON` và `GOOGLE_DRIVE_FOLDER_ID`.
5. Backend upload file, tạo permission `anyone/reader`, rồi lưu `drive_file_id` + `drive_view_url` vào `media`.

> Lưu ý: public sharing của Drive phụ thuộc chính sách Workspace/domain. Nếu domain cấm `anyone`, permission API sẽ bị từ chối. Khi production có traffic ảnh lớn, nên cân nhắc object storage/CDN (S3/R2) thay vì dùng Drive như CDN.

## Social Login
`POST /api/v1/auth/social`
```json
{"provider":"google","token":"GOOGLE_ID_TOKEN","device_name":"nuxt-web"}
```
Hoặc Facebook access token. Backend xác thực token với provider, tìm `user_social_accounts`; nếu chưa tồn tại thì liên kết theo email hoặc tạo `users` mới.

## Checkout atomic
`POST /api/v1/checkout`
```json
{"address_id":1,"payment_method":"COD","note":"Giao giờ hành chính"}
```
Trong 1 transaction:
1. Lock cart + cart_items.
2. Lock từng product, đọc **giá hiện tại**, kiểm tra stock.
3. Tạo `orders`.
4. Copy sang `order_items` và đóng băng `price` + `product_name`.
5. Trừ kho.
6. Tính tổng bằng decimal, cập nhật `orders.total_amount`.
7. Tạo `payments` trạng thái `pending`.
8. Xóa `cart_items`.

Bất kỳ bước nào lỗi → rollback toàn bộ.

## SQL menu đa hình
Logic nằm tại `App\\Services\\HomepageService::menuFlat()`:
```sql
SELECT m.*,
CASE
  WHEN m.category_id IS NOT NULL THEN '/danh-muc/' || c.category_id::text
  WHEN m.product_id IS NOT NULL THEN '/san-pham/' || p.product_id::text
  WHEN m.article_category_id IS NOT NULL THEN '/chuyen-muc/' || ac.slug
  WHEN m.article_id IS NOT NULL THEN '/bai-viet/' || a.slug
  ELSE m.url
END AS resolved_url
FROM menus m
LEFT JOIN categories c ON c.category_id = m.category_id
LEFT JOIN products p ON p.product_id = m.product_id
LEFT JOIN article_categories ac ON ac.article_category_id = m.article_category_id
LEFT JOIN articles a ON a.article_id = m.article_id
WHERE m.is_visible = TRUE
ORDER BY m.position ASC, m.menu_id ASC;
```

## Các endpoint chính
### Public
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/social`
- `GET /api/v1/home`
- `GET /api/v1/products`
- `GET /api/v1/articles`
- `GET/POST/PATCH/DELETE /api/v1/cart...`
- `GET/POST/PUT/DELETE /api/v1/addresses...`
- `GET/POST/DELETE /api/v1/wishlist...`
- `POST /api/v1/profile/avatar`
- `POST /api/v1/checkout`
- `GET /api/v1/orders`

### Admin
- `POST /api/admin/v1/auth/login`
- CRUD `categories`, `products`, `article-categories`, `articles`, `homepage-sections`, `menus`
- `POST /api/admin/v1/media`
- `POST /api/admin/v1/products/{product}/image`
- `POST /api/admin/v1/articles/{article}/thumbnail`
- `POST /api/admin/v1/homepage-sections/reorder`
- `POST /api/admin/v1/menus/reorder`
- `GET/PATCH users`, `orders`, `payments`

## Các ràng buộc DB quan trọng
- `user_addresses`: partial unique index đảm bảo mỗi user tối đa 1 địa chỉ `is_default=true`.
- `user_wishlists`: unique `(user_id, product_id)`.
- `carts.user_id`: unique.
- `cart_items`: unique `(cart_id, product_id)`.
- `payments.order_id`: unique 1-1.
- `products.price >= 0`, `stock_quantity >= 0`.
- `menus`: PostgreSQL `num_nonnulls(...)` đảm bảo tối đa 1 FK đa hình; nếu không có FK thì bắt buộc `url`.
- `orders.user_id`: `ON DELETE RESTRICT`.

## Những cột bổ sung tối thiểu
Mô tả ban đầu không liệt kê đầy đủ cột của `users`, `products`, `articles`, `orders`, `payments`. Project bổ sung các cột tối thiểu cần để API chạy thực tế: `slug`, `sku`, `status`, snapshot địa chỉ giao hàng, snapshot tên sản phẩm, timestamps, v.v. Mọi khóa/ràng buộc bạn nêu vẫn được giữ nguyên.

## Nâng cấp tiếp theo trước production
- Tích hợp webhook/SDK riêng cho VNPay/MoMo và xác minh chữ ký gateway (không cho frontend tự cập nhật payment success).
- Queue cho email/notification và xử lý ảnh.
- Redis cache cho homepage/catalog.
- Idempotency key cho checkout/payment request.
- Audit log admin.
- OpenAPI/Swagger và test integration chạy trên PostgreSQL test DB.

---

## Chạy Development bằng Docker

Project đã có `Dockerfile.dev` + `compose.yaml`, nên không cần cài PHP/Composer/PostgreSQL trực tiếp trên máy.

Khởi động:

```bash
docker compose up -d --build
```

API chạy tại:

```text
http://localhost:8000
http://localhost:8000/api/v1
http://localhost:8000/api/admin/v1
```

Xem log:

```bash
docker compose logs -f app
```

Chạy test:

```bash
docker compose exec app php artisan test
```

Bật queue + scheduler khi cần:

```bash
docker compose --profile workers up -d
```

Bật pgAdmin khi cần:

```bash
docker compose --profile tools up -d pgadmin
```

Xem hướng dẫn đầy đủ tại [`docs/DOCKER_DEV.md`](docs/DOCKER_DEV.md).


## VueJS Admin riêng biệt

Project đã có ứng dụng quản trị độc lập trong `admin/` (Vue 3 + Vite + TypeScript + Pinia).

Chạy toàn bộ backend + PostgreSQL + Admin:

```bash
docker compose up -d --build
```

- Admin UI: http://localhost:5173
- Admin API: http://localhost:8000/api/admin/v1
- Public API: http://localhost:8000/api/v1

Xem `docs/VUE_ADMIN.md` để biết kiến trúc và cách deploy `/admin/`.

---

## Same-domain mode (recommended)

This package now includes an Nginx gateway so the two frontends stay independent while sharing one public origin:

```text
http://localhost:8000/        -> NuxtJS storefront
http://localhost:8000/admin/  -> VueJS admin
http://localhost:8000/api/v1  -> Laravel public API
http://localhost:8000/api/admin/v1 -> Laravel admin API
```

Start:

```bash
cp .env.same-domain.example .env
docker compose up -d --build
```

See `docs/SAME_DOMAIN.md`.
