# Gợi ý triển khai cùng domain

Mục tiêu URL:
- `https://example.com/` → NuxtJS storefront
- `https://example.com/admin/` → VueJS admin SPA
- `https://example.com/api/v1/*` → Laravel public API
- `https://example.com/api/admin/v1/*` → Laravel admin API

## Nginx minh họa
```nginx
server {
    listen 443 ssl http2;
    server_name example.com;

    # Laravel public directory
    location ^~ /api/ {
        root /var/www/ecommerce-api/public;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ ^/api/.*\.php$ {
        root /var/www/ecommerce-api/public;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME /var/www/ecommerce-api/public/index.php;
        fastcgi_param SCRIPT_NAME /index.php;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    }

    # Vue admin build
    location /admin/ {
        alias /var/www/admin-vue/dist/;
        try_files $uri $uri/ /admin/index.html;
    }

    # Nuxt (SSR node server)
    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Tùy hạ tầng thực tế, có thể đặt Laravel ở subdomain `api.example.com`. Route code không đổi, chỉ đổi base URL frontend.

## NuxtJS API client
```ts
export const useApi = () => {
  const config = useRuntimeConfig()
  const token = useCookie<string | null>('access_token')

  return $fetch.create({
    baseURL: config.public.apiBase,
    onRequest({ options }) {
      if (token.value) {
        const headers = new Headers(options.headers)
        headers.set('Authorization', `Bearer ${token.value}`)
        options.headers = headers
      }
    }
  })
}
```

`runtimeConfig.public.apiBase = '/api/v1'` khi cùng domain.

## Vue Admin API client
```ts
import axios from 'axios'

export const adminApi = axios.create({
  baseURL: '/api/admin/v1',
  headers: { Accept: 'application/json' }
})

adminApi.interceptors.request.use((config) => {
  const token = sessionStorage.getItem('admin_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})
```

Ở production nên ưu tiên cơ chế token/cookie chống XSS phù hợp kiến trúc frontend của bạn; không ghi token vào source code.
