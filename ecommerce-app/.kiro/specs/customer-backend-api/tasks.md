# Implementation Plan: Customer Backend API

## Overview

Este plan implementa la API backend completa para gestión de clientes en Laravel 10, siguiendo arquitectura en capas con Controllers, Services, Form Requests y Resources. La implementación se realizará de forma incremental, construyendo cada capa y validando funcionalidad mediante tests.

## Tasks

- [ ] 1. Configurar estructura base y rutas de la API
  - Crear rutas en api.php con middleware auth:sanctum
  - Configurar agrupación de rutas bajo prefijo /api
  - _Requirements: 1.1, 1.2, 1.3_

- [-] 2. Implementar UserController y funcionalidad de usuario
  - [x] 2.1 Crear UserController con método show()
    - Implementar método show() que retorna datos del usuario autenticado
    - Cargar relación customer usando eager loading
    - Retornar respuesta usando UserResource
    - _Requirements: 2.1, 2.2, 2.3_
  
  - [ ]* 2.2 Escribir property test para UserController
    - **Property 2: User Resource Formatting**
    - **Validates: Requirements 2.2**
  
  - [x] 2.3 Crear UpdatePasswordRequest con validaciones
    - Implementar reglas de validación para current_password, password y password_confirmation
    - Agregar mensajes de error personalizados
    - _Requirements: 3.1, 3.2, 3.3_
  
  - [x] 2.4 Crear UserService para lógica de cambio de contraseña
    - Implementar método updatePassword() con validación de contraseña actual
    - Implementar método privado validateCurrentPassword()
    - Usar Hash::check() y Hash::make() para manejo seguro de contraseñas
    - _Requirements: 3.4, 3.5_
  
  - [x] 2.5 Implementar método updatePassword() en UserController
    - Inyectar UserService en constructor
    - Implementar método updatePassword() que usa el service
    - Manejar errores y retornar respuestas apropiadas
    - _Requirements: 3.6_
  
  - [ ]* 2.6 Escribir property test para cambio de contraseña
    - **Property 3: Password Update Validation and Processing**
    - **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

- [x] 3. Implementar Resources para formateo de respuestas
  - [x] 3.1 Crear UserResource
    - Implementar toArray() con campos id, name, email
    - Incluir CustomerResource cuando la relación esté cargada
    - _Requirements: 2.2_
  
  - [x] 3.2 Crear CustomerResource
    - Implementar toArray() con campos del customer
    - _Requirements: 2.2_
  
  - [x] 3.3 Crear AddressResource
    - Implementar toArray() con todos los campos de dirección
    - Incluir campo is_default calculado
    - _Requirements: 4.4, 5.2_
  
  - [x] 3.4 Crear OrderResource y OrderItemResource
    - Implementar OrderResource con campos de orden y relaciones
    - Implementar OrderItemResource con campos de items
    - Usar AddressResource para direcciones de envío y facturación
    - _Requirements: 4.2, 4.3_

- [x] 4. Checkpoint - Verificar funcionalidad básica de usuario
  - Asegurar que todos los tests pasen, preguntar al usuario si surgen dudas.

- [-] 5. Implementar CustomerOrderController y funcionalidad de órdenes
  - [x] 5.1 Crear CustomerService para lógica de órdenes
    - Implementar método getOrders() que retorna órdenes del customer
    - Usar eager loading para cargar relaciones (orderItems, shippingAddress, billingAddress)
    - _Requirements: 4.1_
  
  - [x] 5.2 Crear CustomerOrderController
    - Inyectar CustomerService en constructor
    - Implementar método index() que retorna órdenes del usuario autenticado
    - Usar OrderResource::collection() para formatear respuesta
    - _Requirements: 4.1, 4.2_
  
  - [ ] 5.3 Escribir property test para órdenes del cliente
    - **Property 4: Customer Orders Retrieval and Formatting**
    - **Validates: Requirements 4.1, 4.2**
  
  - [ ]* 5.4 Escribir property test para formateo de items y direcciones
    - **Property 5: Order Items and Address Resource Formatting**
    - **Validates: Requirements 4.3, 4.4**

- [x] 6. Implementar Form Requests para direcciones
  - [x] 6.1 Crear StoreAddressRequest
    - Implementar reglas de validación para todos los campos de dirección
    - Validar campos requeridos y opcionales
    - Validar enum para type (shipping, billing)
    - Validar formato de phone cuando se proporcione
    - _Requirements: 6.1, 6.2, 6.3, 6.4_
  
  - [x] 6.2 Crear UpdateAddressRequest
    - Implementar reglas de validación con 'sometimes' para campos opcionales
    - Reutilizar validaciones de StoreAddressRequest donde sea apropiado
    - _Requirements: 7.1_
  
  - [x] 6.3 Crear SetDefaultAddressRequest
    - Implementar validación para address_id (uuid, exists) y type (enum)
    - _Requirements: 9.1, 9.2_

- [x] 7. Implementar CustomerAddressService para lógica de direcciones
  - [x] 7.1 Crear CustomerAddressService con métodos CRUD básicos
    - Implementar getAddresses() con filtro de soft deletes
    - Implementar createAddress() asociando al customer correcto
    - Implementar updateAddress() con validación de ownership
    - _Requirements: 5.1, 6.5, 7.2, 7.5_
  
  - [x] 7.2 Implementar método deleteAddress() con cleanup de defaults
    - Implementar validación de ownership
    - Implementar cleanup de referencias de dirección por defecto
    - Implementar soft delete
    - _Requirements: 8.1, 8.4, 8.5_
  
  - [x] 7.3 Implementar método setDefaultAddress()
    - Implementar validación de ownership
    - Implementar actualización de campos default_shipping_address_id o default_billing_address_id
    - _Requirements: 9.3, 9.6, 9.7_
  
  - [x] 7.4 Implementar métodos privados de utilidad
    - Implementar validateAddressOwnership()
    - Implementar clearDefaultAddress()
    - _Requirements: 7.2, 8.1, 9.3_

- [x] 8. Implementar CustomerAddressController
  - [x] 8.1 Crear CustomerAddressController con inyección de dependencias
    - Inyectar CustomerAddressService en constructor
    - Configurar middleware auth:sanctum
    - _Requirements: 5.1, 6.5, 7.2, 8.1, 9.3_
  
  - [x] 8.2 Implementar método index() para listar direcciones
    - Obtener customer del usuario autenticado
    - Usar service para obtener direcciones
    - Retornar respuesta con AddressResource::collection()
    - _Requirements: 5.1, 5.2_
  
  - [x] 8.3 Implementar método store() para crear direcciones
    - Validar datos con StoreAddressRequest
    - Usar service para crear dirección
    - Retornar respuesta HTTP 201 con AddressResource
    - Manejar errores apropiadamente
    - _Requirements: 6.5, 6.6_
  
  - [x] 8.4 Implementar método update() para actualizar direcciones
    - Validar datos con UpdateAddressRequest
    - Buscar dirección por UUID
    - Usar service para actualizar dirección
    - Retornar respuesta HTTP 200 con AddressResource
    - Manejar errores 404 y 403 apropiadamente
    - _Requirements: 7.3, 7.4, 7.5, 7.6_
  
  - [x] 8.5 Implementar método destroy() para eliminar direcciones
    - Buscar dirección por UUID
    - Usar service para eliminar dirección
    - Retornar respuesta HTTP 204
    - Manejar errores 404 y 403 apropiadamente
    - _Requirements: 8.2, 8.3, 8.5, 8.6_
  
  - [x] 8.6 Implementar método setDefaultAddress() para configurar dirección por defecto
    - Validar datos con SetDefaultAddressRequest
    - Buscar dirección por UUID
    - Usar service para configurar como por defecto
    - Retornar respuesta HTTP 200 con mensaje de éxito
    - Manejar errores 404 y 403 apropiadamente
    - _Requirements: 9.4, 9.5, 9.8_

- [x] 9. Escribir property tests para funcionalidad de direcciones
  - [x] 9.1 Escribir property test para listado y formateo de direcciones
    - Property 6: Customer Addresses Retrieval and Formatting**
    - Validates: Requirements 5.1, 5.2
  
  - [x] 9.2 Escribir property test para creación de direcciones
    - **Property 7: Address Creation Validation and Processing**
    - **Validates: Requirements 6.1, 6.2, 6.3, 6.4, 6.5, 6.6**
  
  - [ ]* 9.3 Escribir property test para actualización de direcciones
    - **Property 8: Address Update with Ownership Verification**
    - **Validates: Requirements 7.1, 7.2, 7.3, 7.4, 7.5, 7.6**
  
  - [x] 9.4 Escribir property test para eliminación de direcciones
    - **Property 9: Address Deletion with Default Address Cleanup**
    - **Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5, 8.6**
  
  - [ ]* 9.5 Escribir property test para configuración de dirección por defecto
    - **Property 10: Default Address Configuration**
    - **Validates: Requirements 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7, 9.8**

- [x] 10. Implementar manejo global de errores y respuestas consistentes
  - [x] 10.1 Configurar exception handling en Handler.php
    - Manejar ModelNotFoundException para retornar 404
    - Manejar AuthenticationException para retornar 401
    - Manejar AuthorizationException para retornar 403
    - Manejar ValidationException para retornar 422
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_
  
  - [x] 10.2 Escribir property test para manejo consistente de errores
    - **Property 11: Consistent Error Response Formatting**
    - **Validates: Requirements 10.1, 10.2, 10.3, 10.4, 10.5**

- [ ] 11. Escribir property test para autenticación y autorización
  - [ ]* 11.1 Escribir property test para autenticación y autorización
    - **Property 1: Authentication and Authorization**
    - **Validates: Requirements 1.2, 1.3, 7.3, 8.2, 9.4**

- [ ] 12. Checkpoint final - Verificar integración completa
  - Asegurar que todos los tests pasen, verificar que todas las rutas funcionen correctamente, preguntar al usuario si surgen dudas.

- [ ] 13. Documentar código con PHPDoc
  - [ ] 13.1 Agregar PHPDoc a Controllers
    - Documentar propósito de cada método
    - Documentar parámetros y valores de retorno
    - _Requirements: 12.1_
  
  - [ ] 13.2 Agregar PHPDoc a Services
    - Documentar parámetros, retornos y excepciones
    - Documentar lógica de negocio compleja
    - _Requirements: 12.2_
  
  - [ ] 13.3 Agregar PHPDoc a Form Requests
    - Documentar reglas de validación
    - Documentar propósito de cada request
    - _Requirements: 12.3_
  
  - [ ] 13.4 Agregar PHPDoc a Resources
    - Documentar estructura de respuesta
    - Documentar transformaciones aplicadas
    - _Requirements: 12.4_

## Notes

- Las tareas marcadas con `*` son opcionales y pueden omitirse para un MVP más rápido
- Cada tarea referencia requerimientos específicos para trazabilidad
- Los checkpoints aseguran validación incremental
- Los property tests validan propiedades universales de corrección
- Los unit tests validan ejemplos específicos y casos edge
- La implementación sigue principios SOLID con separación clara de responsabilidades
- Todos los endpoints están protegidos con middleware auth:sanctum
- Se usa soft delete para direcciones para mantener integridad referencial
- Los UUIDs se usan para identificación pública de direcciones por seguridad