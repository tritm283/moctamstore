# Docker Development

Môi trường dev không yêu cầu cài PHP, Composer hoặc PostgreSQL trên máy host.

## 1. Yêu cầu

- Docker Desktop (Windows/macOS) hoặc Docker Engine + Compose plugin (Linux)
- Git nếu bạn dùng source qua repository

## 2. Khởi động lần đầu

Tại thư mục project:

```bash
docker compose up -d --build
```

Container `app` sẽ tự:

1. Chờ PostgreSQL sẵn sàng.
2. Cài Composer packages vào Docker volume `vendor` nếu chưa có.
3. Chạy `php artisan optimize:clear`.
4. Chạy migration.
5. Chạy seeder dev.
6. Mở Laravel tại `http://localhost:8000`.

Kiểm tra:

```bash
docker compose ps
curl http://localhost:8000/up
```

Windows PowerShell có thể mở trực tiếp `http://localhost:8000/up` trên trình duyệt nếu không có `curl`.

## 3. URL dev

- Laravel API: `http://localhost:8000`
- Health check: `http://localhost:8000/up`
- NuxtJS dự kiến: `http://localhost:3000`
- Vue Admin dự kiến: `http://localhost:5173`
- PostgreSQL host từ máy thật: `localhost:5432`
- PostgreSQL host từ container: `postgres:5432`

Public API:

```text
http://localhost:8000/api/v1/...
```

Admin API:

```text
http://localhost:8000/api/admin/v1/...
```

## 4. Admin mặc định

Nếu không override biến môi trường:

```text
Email: admin@example.com
Password: ChangeMe123!
```

Chỉ dùng thông tin này trong môi trường dev.

## 5. File .env cho Docker Compose

Không bắt buộc. Compose đã có giá trị dev mặc định.

Nếu muốn đổi port/password/cấu hình social:

```bash
cp .env.docker.example .env
```

Trên PowerShell:

```powershell
Copy-Item .env.docker.example .env
```

Sau đó sửa `.env` rồi chạy:

```bash
docker compose up -d
```

## 6. Google Drive trong Docker

Đặt service-account JSON vào:

```text
storage/app/google-service-account.json
```

Trong container file sẽ có đường dẫn:

```text
/var/www/html/storage/app/google-service-account.json
```

Sau đó cấu hình ít nhất:

```dotenv
GOOGLE_DRIVE_FOLDER_ID=your_folder_id
GOOGLE_CLIENT_ID=your_google_client_id
```

Không commit service-account JSON lên Git.

## 7. Các lệnh thường dùng

Xem log Laravel:

```bash
docker compose logs -f app
```

Vào shell container:

```bash
docker compose exec app bash
```

Artisan:

```bash
docker compose exec app php artisan route:list
docker compose exec app php artisan migrate:status
docker compose exec app php artisan test
```

Composer:

```bash
docker compose exec app composer install
docker compose exec app composer update
```

Tinker:

```bash
docker compose exec app php artisan tinker
```

Reset database hoàn toàn:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Dừng container:

```bash
docker compose down
```

Dừng và xóa cả database dev + vendor volume:

```bash
docker compose down -v
```

> `down -v` xóa dữ liệu PostgreSQL dev. Không dùng nếu muốn giữ dữ liệu.

## 8. Queue + Scheduler

Mặc định không bật để dev nhẹ.

Khi cần queue/scheduler:

```bash
docker compose --profile workers up -d
```

Kiểm tra:

```bash
docker compose ps
```

## 9. pgAdmin tùy chọn

Bật pgAdmin:

```bash
docker compose --profile tools up -d pgadmin
```

Mở:

```text
http://localhost:5050
```

Mặc định:

```text
Login: admin@example.com
Password: admin123456
```

Tạo server trong pgAdmin:

```text
Host: postgres
Port: 5432
Database: ecommerce_cms
Username: postgres
Password: postgres
```

## 10. Chạy cùng NuxtJS và Vue Admin trên máy host

Trong giai đoạn đầu, cách nhanh nhất là:

- Backend/PostgreSQL chạy Docker.
- Nuxt chạy port `3000`.
- Vue Admin chạy port `5173`.

Nuxt `.env` ví dụ:

```dotenv
NUXT_PUBLIC_API_BASE=http://localhost:8000/api/v1
```

Vue Admin `.env.development` ví dụ:

```dotenv
VITE_API_BASE_URL=http://localhost:8000/api/admin/v1
```

Laravel đã cho phép CORS từ hai origin này qua `FRONTEND_URL` và `ADMIN_URL`.

Khi frontend source đã có, có thể thêm hai service Node vào `compose.yaml` để chạy full stack hoàn toàn trong Docker.

## 11. Hot reload backend

Source được mount:

```yaml
- ./:/var/www/html
```

Do đó sửa PHP trên host là Laravel nhận code mới ngay, không cần rebuild image.

Chỉ rebuild khi thay đổi:

- `Dockerfile.dev`
- PHP extension/system package
- `docker/php/php.ini`

Lệnh rebuild:

```bash
docker compose up -d --build app
```

## 12. Lỗi thường gặp trên Windows

### Port 5432 đã được PostgreSQL khác sử dụng

Đổi trong `.env`:

```dotenv
POSTGRES_PORT=5433
```

Laravel trong container vẫn dùng `postgres:5432`; chỉ port truy cập từ Windows đổi thành `5433`.

### Port 8000 bị chiếm

```dotenv
APP_PORT=8080
APP_URL=http://localhost:8080
```

Sau đó Nuxt/Vue cũng đổi API base sang port 8080.

### Muốn cài Composer lại từ đầu

```bash
docker compose down
docker volume rm ecommerce-cms-dev_vendor
# hoặc đơn giản:
docker compose down -v
```

Lưu ý `down -v` cũng xóa PostgreSQL dev.

## 13. Full stack Docker khi đã có source Nuxt/Vue

Project kèm file mẫu:

```text
compose.frontend.example.yaml
```

File này giả định cấu trúc:

```text
project/
├─ frontend/   # NuxtJS
├─ admin/      # VueJS
├─ app/        # Laravel app hiện tại nằm ở root
├─ compose.yaml
└─ compose.frontend.example.yaml
```

Khi hai thư mục frontend đã tồn tại, chạy:

```bash
docker compose -f compose.yaml -f compose.frontend.example.yaml up -d --build
```

Khi đó toàn bộ dev stack gồm:

```text
Nuxt storefront : http://localhost:3000
Vue Admin       : http://localhost:5173
Laravel API     : http://localhost:8000
PostgreSQL      : localhost:5432
```

Không chạy overlay này khi `frontend/` và `admin/` chưa có source thực tế.
