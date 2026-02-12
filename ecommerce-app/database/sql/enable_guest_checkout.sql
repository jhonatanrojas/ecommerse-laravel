-- Script para habilitar Guest Checkout
-- Ejecutar este script si deseas habilitar el checkout de invitados por defecto

UPDATE store_settings 
SET allow_guest_checkout = 1 
WHERE id = 1;

-- Verificar el cambio
SELECT id, store_name, allow_guest_checkout 
FROM store_settings;
