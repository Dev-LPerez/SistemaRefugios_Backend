# Documentación Fase 1: Core de Seguridad, Roles y Auditoría

## Objetivo
Proteger los endpoints existentes mediante autenticación, establecer el control de acceso basado en roles y habilitar la trazabilidad completa de las acciones a través de un sistema de auditoría transversal (aspect-oriented-like).

## Acciones Realizadas

### 1. Actualización de DTOs y Servicios de Usuario (Bcrypt)
- Se actualizaron las clases de transferencia de datos `CreateUsuarioDTO.php` y `UpdateUsuarioDTO.php` para integrar el encriptado y *hashing* de contraseñas de forma automática desde la persistencia utilizando `password_hash(..., PASSWORD_BCRYPT)`.
- Se adaptó `UsuarioService.php` para utilizar el password directamente ya encriptado de las entidades entrantes.

### 2. Creación del Controlador y Sistema de Autenticación
- Se crearon los métodos de `login` en `UsuarioService.php` con verificación de contraseña a través del comparador criptográfico nativo `password_verify(...)`.
- Se introdujeron validaciones de Auth Actions en `UsuarioController.php` posibilitando el Endpoint de `POST /api/usuarios?action=login`.

### 3. Middleware de Protección JWT y Roles
- Se escribió la clase estática `AuthMiddleware.php` en el directorio `src/middlewares`. 
- Posee la responsabilidad de inyectar y verificar la integridad de los *Beared Tokens* en la Cabecera (Header) de la petición HTTP.
- Cuenta con el método `checkRole()` para la protección y autorización basada en perfiles del sistema (`Admin`, `Operario`, etc.).

### 4. Sistema Centralizado de Auditoría
- Se crearon las entidades base: `src/auditoria/entity/LogAuditoria.php` y sus respectivo Controlador `AuditoriaController.php` y Servicio `AuditoriaService.php`.
- Exponen una ruta `GET /api/auditoria` que es privada de forma estricta para uso único de perfiles definidos como `Admin` o `Auditor`. 

### 5. Inyección de Seguridad Transversal (Front-Controller)
Con el fin de evitar un acoplamiento estricto y la repetición de código manual en todos los controladores pre-existentes, se configuró el punto de acceso central `index.php`:
1. Se establecieron los checkeos obligatorios validando el permiso respectivo por el enrutador (`$route`) y protegiendo sus endpoints y delegando al middleware la inspección de token de la petición.
2. Todas las peticiones mutacionales (`POST`, `PUT`, `DELETE`) en todo el sistema se interceptan en el front-door, inyectando de forma silenciosa un log con los detalles del usuario que ejecutó dicha acción, su IP remota y la entidad mutada, cumpliendo al 100% el *RF-05.03* y la *Tarea 1.3*.

## Arquitectura Resultante
- Endpoint Público:  `?route=usuarios&action=login`
- Endpoints Protegidos: Todas las mutaciones o listados requieren ser acompañadas del header `Authorization: Bearer <token_aqui>`.

---

## Próximos Pasos
Culminado el Core de Seguridad y comprobado que ninguna mutación a la Base de Datos es anónima, el sistema puede seguir hacia la **Fase 2: Adaptación del Censo y Preparación Offline**.