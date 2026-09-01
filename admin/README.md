# VueJS Admin Console

Ứng dụng quản trị **tách riêng hoàn toàn** khỏi website người dùng NuxtJS.

## Dev

```bash
npm install
npm run dev
```

Mặc định:

- Admin UI: http://localhost:5173
- Laravel Admin API: http://localhost:8000/api/admin/v1

Copy `.env.example` thành `.env` nếu chạy Node trực tiếp.

## Docker

Từ thư mục gốc project:

```bash
docker compose up -d --build
```

Docker chạy `app`, `postgres` và `admin`. Không cần cài Node/PHP/PostgreSQL trên máy host.

## Production dưới `/admin/`

Build với:

```bash
VITE_API_BASE_URL=/api/admin/v1 VITE_APP_BASE=/admin/ npm run build
```

Router dùng `createWebHistory(import.meta.env.BASE_URL)`, vì vậy build `/admin/` không ảnh hưởng route của NuxtJS ở `/`.
