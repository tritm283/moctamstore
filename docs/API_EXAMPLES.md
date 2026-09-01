# API examples

## Register
`POST /api/v1/auth/register`
```json
{"full_name":"Nguyen Van A","email":"a@example.com","phone":"0900000000","password":"StrongPass123!","password_confirmation":"StrongPass123!","is_marketing_allowed":true}
```

## Add cart item
`POST /api/v1/cart/items` + Bearer token
```json
{"product_id":10,"quantity":2}
```

## Checkout
`POST /api/v1/checkout` + Bearer token
```json
{"address_id":3,"payment_method":"COD","note":"Gọi trước khi giao"}
```

## Create dynamic menu
`POST /api/admin/v1/menus`
```json
{"title":"Thời trang nam","category_id":5,"product_id":null,"article_category_id":null,"article_id":null,"url":null,"parent_id":null,"position":1,"is_visible":true}
```
DB sẽ tự từ chối nếu gửi đồng thời nhiều hơn một target FK.

## Homepage product carousel
`POST /api/admin/v1/homepage-sections`
```json
{"title":"Sản phẩm nổi bật","type":"product_carousel","position":3,"is_visible":true,"settings":{"limit":8,"category_id":5}}
```
