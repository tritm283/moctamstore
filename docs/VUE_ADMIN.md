# Kiến trúc VueJS Admin tách biệt

## Cấu trúc

```text
project/
├─ admin/                  # Vue 3 + Vite + TypeScript + Pinia riêng
├─ app/                    # Laravel backend
├─ routes/admin.php        # /api/admin/v1/*
├─ routes/public.php       # /api/v1/*
├─ database/               # PostgreSQL migrations
└─ compose.yaml
```

Website người dùng NuxtJS không nằm trong `admin/` và không import bất kỳ component/router/store nào của Admin.

## Bảo mật API

- Login Admin: `POST /api/admin/v1/auth/login`
- Các route còn lại: `auth:sanctum + admin`
- Token lưu dưới key `ecommerce_admin_token`
- Axios tự thêm `Authorization: Bearer ...`
- HTTP 401 tự xóa token và chuyển về `/login`
- Backend tiếp tục là nơi quyết định quyền, không tin frontend.

## Module giao diện đã có

- Dashboard
- Sản phẩm
- Danh mục sản phẩm
- Bài viết CMS
- Chuyên mục bài viết
- Đơn hàng
- Thanh toán
- Khách hàng
- Media Google Drive
- Homepage Sections
- Menu cây đa hình

## Dev Docker

```bash
docker compose up -d --build
```

Truy cập:

- Admin: `http://localhost:5173`
- Laravel API: `http://localhost:8000`
- PostgreSQL: `localhost:5432`

Tài khoản seed mặc định:

```text
admin@example.com
ChangeMe123!
```

## Production

Khuyến nghị vẫn giữ hai ứng dụng frontend độc lập:

```text
https://domain.com/          -> NuxtJS storefront
https://domain.com/admin/   -> VueJS Admin build
https://domain.com/api/v1/  -> Public API
https://domain.com/api/admin/v1/ -> Admin API
```

Admin build với `VITE_APP_BASE=/admin/` và `VITE_API_BASE_URL=/api/admin/v1`.
