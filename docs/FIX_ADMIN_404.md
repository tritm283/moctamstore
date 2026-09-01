# Fix `/admin` 404 in Docker development

If `http://localhost:8000/admin` returns Laravel `404 Not Found`, port 8000 is pointing directly to Laravel from an old compose configuration/container.

The correct same-domain topology is:

- `http://localhost:8000/` -> Nginx Gateway -> Nuxt storefront
- `http://localhost:8000/admin/` -> Nginx Gateway -> Vue Admin
- `http://localhost:8000/api/v1/...` -> Nginx Gateway -> Laravel
- `http://localhost:8000/api/admin/v1/...` -> Nginx Gateway -> Laravel Admin API
- Laravel's port 8000 is exposed only inside the Docker network, not published directly to the host.

## Clean restart

```bash
docker compose down --remove-orphans
# Optional: inspect old containers that still publish port 8000
docker ps --format "table {{.Names}}\t{{.Ports}}"
docker compose up -d --build
```

Then verify:

```bash
docker compose ps
```

The `gateway` service should publish `0.0.0.0:8000->80/tcp`.
The `app` service should show only internal port `8000/tcp`, not `0.0.0.0:8000->8000/tcp`.

Open:

- Storefront: `http://localhost:8000/`
- Admin: `http://localhost:8000/admin/`

If port 8000 is already occupied by an unrelated service, either stop that service or set `GATEWAY_PORT=8080` in `.env` and use `http://localhost:8080/admin/`.
