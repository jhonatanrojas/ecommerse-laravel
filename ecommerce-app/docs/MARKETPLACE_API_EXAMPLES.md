# Marketplace API (B2B) - Ejemplos

## 1) Registro de vendedor
`POST /api/marketplace/vendors/register`

Payload:
```json
{
  "name": "Carlos Perez",
  "email": "carlos@tienda-b2b.com",
  "password": "Secret1234",
  "business_name": "Distribuidora Perez",
  "document": "J-12345678-9",
  "phone": "+58 424 0000000",
  "address": "Caracas, Venezuela"
}
```

Respuesta 201:
```json
{
  "success": true,
  "message": "Registro de vendedor recibido.",
  "data": {
    "uuid": "7dfd2f5a-0f45-4a1e-a0b9-8cf43a0ea216",
    "status": "pending",
    "business_name": "Distribuidora Perez"
  }
}
```

## 2) Perfil público del vendedor
`GET /api/marketplace/vendors/{vendor_uuid}/profile`

Respuesta 200:
```json
{
  "success": true,
  "data": {
    "uuid": "7dfd2f5a-0f45-4a1e-a0b9-8cf43a0ea216",
    "business_name": "Distribuidora Perez",
    "phone": "+58 424 0000000",
    "email": "carlos@tienda-b2b.com",
    "address": "Caracas, Venezuela",
    "joined_at": "2026-02-13T14:20:31+00:00"
  }
}
```

## 3) Productos por vendedor
`GET /api/marketplace/vendors/{vendor_uuid}/products?per_page=12&page=1`

Respuesta 200 (fragmento):
```json
{
  "success": true,
  "data": [
    {
      "id": 101,
      "name": "Teclado Mecánico RGB",
      "slug": "teclado-mecanico-rgb",
      "price": "79.99",
      "stock": 40,
      "status": "active"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "total": 29
  }
}
```

## 4) Productos del marketplace con filtros
`GET /api/marketplace/products?vendor_uuid={vendor_uuid}&category_id=3&search=teclado`

Respuesta 200 (fragmento):
```json
{
  "success": true,
  "data": [
    {
      "id": 101,
      "name": "Teclado Mecánico RGB",
      "price": "79.99",
      "vendor": {
        "uuid": "7dfd2f5a-0f45-4a1e-a0b9-8cf43a0ea216",
        "business_name": "Distribuidora Perez"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 1,
    "filters": {
      "vendor_uuid": "7dfd2f5a-0f45-4a1e-a0b9-8cf43a0ea216",
      "category_id": "3",
      "search": "teclado"
    }
  }
}
```

## 5) Checkout con split por vendedor (respuesta)
`POST /api/cart/checkout`

Respuesta 201 (fragmento):
```json
{
  "success": true,
  "message": "Checkout completed successfully",
  "data": {
    "uuid": "d0f5c313-8cd8-4fce-bb2f-42e8d22df0e5",
    "order_number": "ORD-20260213-A1B2C3",
    "vendor_orders": [
      {
        "vendor_id": 2,
        "subtotal": 200,
        "commission_amount": 20,
        "vendor_earnings": 180,
        "payout_status": "pending",
        "shipping_status": "pending"
      }
    ]
  }
}
```
