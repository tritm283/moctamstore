# Same-domain architecture

## URL design

Development (default):

- Storefront NuxtJS: `http://localhost:8000/`
- VueJS Admin: `http://localhost:8000/admin/`
- Public API: `http://localhost:8000/api/v1/...`
- Admin API: `http://localhost:8000/api/admin/v1/...`

Production:

- Storefront: `https://domain.com/`
- Admin: `https://domain.com/admin/`
- Public API: `https://domain.com/api/v1/...`
- Admin API: `https://domain.com/api/admin/v1/...`

## Why a gateway is used

`gateway` is the only browser-facing HTTP service. Nginx routes requests internally:

```text
browser
   |
   v
Nginx gateway :80
   |-- /admin/*       -> Vue/Vite :5173
   |-- /api/*         -> Laravel :8000
   `-- everything else -> Nuxt :3000
```

This keeps NuxtJS and VueJS Admin as two independent applications while giving users one domain.

## Start development

```bash
cp .env.same-domain.example .env
docker compose up -d --build
```

Open only the gateway URL:

```text
http://localhost:8000/
http://localhost:8000/admin/
```

Do not configure browser-side code to use `localhost:3000`, `localhost:5173` or `localhost:8000`.
All browser API URLs should be relative `/api/...` URLs.

## Use port 80 locally

Set:

```env
GATEWAY_PORT=80
APP_URL=http://localhost
FRONTEND_URL=http://localhost
ADMIN_URL=http://localhost
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

Then restart:

```bash
docker compose down
docker compose up -d --build
```

If Windows already uses port 80, keep the default port 8080.

## Production build of Vue Admin

Admin is compiled with:

```env
VITE_APP_BASE=/admin/
VITE_API_BASE_URL=/api/admin/v1
```

The Vue router therefore resolves routes as:

```text
/admin/login
/admin/dashboard
/admin/products
/admin/orders
```

It never takes ownership of storefront URLs outside `/admin/`.
