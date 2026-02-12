# Requirements Document

## Introduction

Este documento especifica los requerimientos para la implementación de la API backend de gestión de clientes (Customer) en Laravel 10. El sistema permitirá a los clientes autenticados gestionar su perfil, contraseña, direcciones y consultar sus órdenes, siguiendo principios SOLID y arquitectura en capas (Controllers, Services, Form Requests, Resources).

## Glossary

- **API**: Application Programming Interface - interfaz REST para comunicación cliente-servidor
- **Customer**: Cliente registrado en el sistema con perfil de comprador
- **User**: Usuario autenticado mediante Laravel Sanctum
- **Address**: Dirección postal del cliente (envío o facturación)
- **Order**: Orden de compra realizada por el cliente
- **OrderItem**: Producto individual dentro de una orden
- **Controller**: Capa de presentación que maneja requests HTTP
- **Service**: Capa de lógica de negocio que implementa operaciones del dominio
- **FormRequest**: Clase de validación de Laravel para requests HTTP
- **Resource**: Clase de transformación de Laravel para formatear respuestas JSON
- **Sanctum**: Sistema de autenticación de API de Laravel
- **SoftDelete**: Eliminación lógica que marca registros como eliminados sin borrarlos físicamente
- **DefaultAddress**: Dirección configurada como predeterminada para envío o facturación

## Requirements

### Requirement 1: Autenticación de Usuario

**User Story:** Como cliente, quiero autenticarme en la API, para que pueda acceder a mis datos y realizar operaciones protegidas.

#### Acceptance Criteria

1. THE API SHALL proteger todos los endpoints con middleware auth:sanctum
2. WHEN un usuario no autenticado intenta acceder a un endpoint protegido, THEN THE API SHALL retornar código HTTP 401 con mensaje de error
3. WHEN un token de autenticación es válido, THEN THE API SHALL permitir el acceso al recurso solicitado

### Requirement 2: Consulta de Perfil de Usuario

**User Story:** Como cliente autenticado, quiero consultar mi perfil de usuario, para que pueda ver mis datos personales y de customer.

#### Acceptance Criteria

1. WHEN un usuario autenticado solicita GET /api/user, THEN THE API SHALL retornar los datos del usuario con su relación customer
2. THE UserResource SHALL formatear la respuesta incluyendo id, name, email, y datos del customer
3. WHEN el usuario no tiene un customer asociado, THEN THE API SHALL retornar los datos del usuario sin relación customer

### Requirement 3: Cambio de Contraseña

**User Story:** Como cliente autenticado, quiero cambiar mi contraseña, para que pueda mantener mi cuenta segura.

#### Acceptance Criteria

1. WHEN un usuario envía PUT /api/user/password con current_password, password y password_confirmation, THEN THE API SHALL validar los tres campos
2. THE UpdatePasswordRequest SHALL validar que current_password sea requerido
3. THE UpdatePasswordRequest SHALL validar que password tenga mínimo 8 caracteres y sea confirmado
4. WHEN la contraseña actual es incorrecta, THEN THE API SHALL retornar código HTTP 422 con mensaje de error
5. WHEN la contraseña actual es correcta y la nueva es válida, THEN THE UserService SHALL actualizar la contraseña del usuario
6. WHEN la contraseña se actualiza exitosamente, THEN THE API SHALL retornar código HTTP 200 con mensaje de éxito

### Requirement 4: Consulta de Órdenes del Cliente

**User Story:** Como cliente autenticado, quiero consultar mis órdenes, para que pueda revisar mi historial de compras.

#### Acceptance Criteria

1. WHEN un cliente autenticado solicita GET /api/customer/orders, THEN THE API SHALL retornar todas las órdenes del usuario autenticado
2. THE OrderResource SHALL formatear cada orden incluyendo id, order_number, status, total, created_at, items, shipping_address y billing_address
3. THE OrderItemResource SHALL formatear cada item incluyendo product_name, quantity, price y subtotal
4. THE AddressResource SHALL formatear las direcciones de envío y facturación
5. WHEN el cliente no tiene órdenes, THEN THE API SHALL retornar un array vacío con código HTTP 200

### Requirement 5: Listado de Direcciones del Cliente

**User Story:** Como cliente autenticado, quiero listar mis direcciones guardadas, para que pueda ver todas mis direcciones disponibles.

#### Acceptance Criteria

1. WHEN un cliente autenticado solicita GET /api/customer/addresses, THEN THE API SHALL retornar todas las direcciones del customer que no estén soft-deleted
2. THE AddressResource SHALL formatear cada dirección incluyendo uuid, first_name, last_name, company, phone, address_line1, address_line2, city, state, postal_code, country, type, is_default
3. WHEN el cliente no tiene direcciones, THEN THE API SHALL retornar un array vacío con código HTTP 200

### Requirement 6: Creación de Dirección

**User Story:** Como cliente autenticado, quiero crear una nueva dirección, para que pueda usarla en futuras compras.

#### Acceptance Criteria

1. WHEN un cliente envía POST /api/customer/addresses con datos de dirección, THEN THE StoreAddressRequest SHALL validar todos los campos requeridos
2. THE StoreAddressRequest SHALL validar que first_name, last_name, address_line1, city, state, postal_code y country sean requeridos
3. THE StoreAddressRequest SHALL validar que phone sea opcional pero con formato válido si se proporciona
4. THE StoreAddressRequest SHALL validar que type sea enum válido (shipping o billing)
5. WHEN los datos son válidos, THEN THE CustomerAddressService SHALL crear la dirección asociada al customer del usuario autenticado
6. WHEN la dirección se crea exitosamente, THEN THE API SHALL retornar código HTTP 201 con la dirección creada formateada por AddressResource

### Requirement 7: Actualización de Dirección

**User Story:** Como cliente autenticado, quiero actualizar una dirección existente, para que pueda corregir o modificar mis datos.

#### Acceptance Criteria

1. WHEN un cliente envía PUT /api/customer/addresses/{uuid} con datos actualizados, THEN THE UpdateAddressRequest SHALL validar los campos proporcionados
2. THE API SHALL verificar que la dirección pertenezca al customer del usuario autenticado
3. WHEN la dirección no pertenece al usuario, THEN THE API SHALL retornar código HTTP 403 con mensaje de error
4. WHEN la dirección no existe, THEN THE API SHALL retornar código HTTP 404 con mensaje de error
5. WHEN los datos son válidos y la dirección pertenece al usuario, THEN THE CustomerAddressService SHALL actualizar la dirección
6. WHEN la actualización es exitosa, THEN THE API SHALL retornar código HTTP 200 con la dirección actualizada formateada por AddressResource

### Requirement 8: Eliminación de Dirección

**User Story:** Como cliente autenticado, quiero eliminar una dirección, para que pueda remover direcciones que ya no uso.

#### Acceptance Criteria

1. WHEN un cliente envía DELETE /api/customer/addresses/{uuid}, THEN THE API SHALL verificar que la dirección pertenezca al customer del usuario autenticado
2. WHEN la dirección no pertenece al usuario, THEN THE API SHALL retornar código HTTP 403 con mensaje de error
3. WHEN la dirección no existe, THEN THE API SHALL retornar código HTTP 404 con mensaje de error
4. WHEN la dirección es una dirección por defecto del customer, THEN THE CustomerAddressService SHALL remover la referencia antes de eliminar
5. WHEN la dirección pertenece al usuario, THEN THE CustomerAddressService SHALL realizar soft delete de la dirección
6. WHEN la eliminación es exitosa, THEN THE API SHALL retornar código HTTP 204 sin contenido

### Requirement 9: Configuración de Dirección por Defecto

**User Story:** Como cliente autenticado, quiero configurar una dirección como predeterminada, para que se use automáticamente en mis compras.

#### Acceptance Criteria

1. WHEN un cliente envía PUT /api/customer/default-address con address_id y type, THEN THE SetDefaultAddressRequest SHALL validar ambos campos
2. THE SetDefaultAddressRequest SHALL validar que type sea 'shipping' o 'billing'
3. THE API SHALL verificar que la dirección pertenezca al customer del usuario autenticado
4. WHEN la dirección no pertenece al usuario, THEN THE API SHALL retornar código HTTP 403 con mensaje de error
5. WHEN la dirección no existe, THEN THE API SHALL retornar código HTTP 404 con mensaje de error
6. WHEN type es 'shipping', THEN THE CustomerAddressService SHALL actualizar default_shipping_address_id del customer
7. WHEN type es 'billing', THEN THE CustomerAddressService SHALL actualizar default_billing_address_id del customer
8. WHEN la configuración es exitosa, THEN THE API SHALL retornar código HTTP 200 con mensaje de éxito

### Requirement 10: Manejo de Errores y Respuestas Consistentes

**User Story:** Como desarrollador frontend, quiero recibir respuestas de error consistentes, para que pueda manejar errores de forma predecible.

#### Acceptance Criteria

1. WHEN ocurre un error de validación, THEN THE API SHALL retornar código HTTP 422 con estructura estándar de Laravel (message, errors)
2. WHEN ocurre un error de autenticación, THEN THE API SHALL retornar código HTTP 401 con mensaje descriptivo
3. WHEN ocurre un error de autorización, THEN THE API SHALL retornar código HTTP 403 con mensaje descriptivo
4. WHEN un recurso no existe, THEN THE API SHALL retornar código HTTP 404 con mensaje descriptivo
5. WHEN ocurre un error interno del servidor, THEN THE API SHALL retornar código HTTP 500 con mensaje genérico

### Requirement 11: Arquitectura en Capas y Principios SOLID

**User Story:** Como desarrollador, quiero que el código siga principios SOLID, para que sea mantenible y extensible.

#### Acceptance Criteria

1. THE Controllers SHALL delegar la lógica de negocio a Services mediante inyección de dependencias
2. THE Services SHALL contener toda la lógica de negocio y operaciones del dominio
3. THE FormRequests SHALL contener todas las reglas de validación de entrada
4. THE Resources SHALL contener toda la lógica de transformación de respuestas
5. WHEN se necesite modificar lógica de negocio, THEN solo se deberá modificar el Service correspondiente
6. THE Services SHALL usar transacciones de base de datos cuando se requieran operaciones atómicas

### Requirement 12: Documentación de Código

**User Story:** Como desarrollador, quiero que el código esté documentado, para que pueda entender su funcionamiento fácilmente.

#### Acceptance Criteria

1. THE Controllers SHALL tener PHPDoc describiendo el propósito de cada método
2. THE Services SHALL tener PHPDoc describiendo parámetros, retornos y excepciones
3. THE FormRequests SHALL tener PHPDoc describiendo las reglas de validación
4. THE Resources SHALL tener PHPDoc describiendo la estructura de la respuesta
