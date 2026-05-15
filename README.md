# SistemaRefugios — Backend API REST

Backend del Sistema de Gestión de Refugios desarrollado en **PHP puro (Vanilla PHP)** con arquitectura modular en capas. Provee una API RESTful completa para la gestión de damnificados, inventario, donaciones y entregas en situaciones de emergencia humanitaria.

---

## Stack Tecnológico

| Componente | Tecnología |
|---|---|
| Lenguaje | PHP (Vanilla PHP) |
| Base de Datos | MySQL vía PDO |
| Autenticación | JWT (`firebase/php-jwt`) |
| Servidor Local | Apache + XAMPP |
| Despliegue | Docker / Render |
| Patrón de diseño | Front Controller + MVC por capas |

---

## Arquitectura

Todas las peticiones HTTP ingresan por `index.php` (Front Controller), que actúa como enrutador central. Cada módulo está organizado en:

- **Controller** — Recibe la petición HTTP, valida el método y delega al servicio.
- **Service** — Contiene la lógica de negocio y accede a la base de datos mediante PDO.
- **DTO (Data Transfer Object)** — Valida y estructura los datos de entrada antes de procesarlos.
- **Entity** — Representa la estructura de cada entidad del dominio.

```
index.php (Front Controller + Router)
└── src/
    ├── config/database.php         ← Conexión PDO
    ├── middlewares/AuthMiddleware.php  ← JWT (generación y validación)
    ├── refugios/
    ├── familias/
    ├── donaciones/
    ├── entregas/
    ├── recursos/
    ├── usuarios/
    ├── gestiones/
    ├── auditoria/
    ├── priorizacion/
    └── reportes/
```

---

## Instalación Local (XAMPP)

1. Clona el repositorio en `C:\xampp\htdocs\`:
   ```bash
   git clone https://github.com/Dev-LPerez/SistemaRefugios_Backend.git
   ```
2. Instala las dependencias PHP:
   ```bash
   composer install
   ```
3. Importa el esquema de base de datos en phpMyAdmin o MySQL:
   ```bash
   mysql -u root -p sistema_refugios < database/sistema_refugios.sql
   ```
4. Configura las credenciales en `src/config/database.php`:
   ```php
   $host     = 'localhost';
   $db_name  = 'sistema_refugios';
   $username = 'root';
   $password = '';
   ```
5. Asegúrate de tener habilitado `mod_rewrite` en Apache para que el `.htaccess` funcione.
6. Inicia Apache y MySQL desde el panel de XAMPP.
7. Accede a la API en: `http://localhost/Backend_Refugios/`

---

## Seguridad

- **Autenticación JWT** — El endpoint `POST /login` devuelve un token con vigencia de 24 horas.
- **Control de roles** — `AuthMiddleware::checkRole()` valida que el rol del token coincida con el permitido para cada operación.
- **Contraseñas con bcrypt** — `password_hash()` en la creación de usuarios y `password_verify()` en el login.
- **CORS** — Habilitado con `Access-Control-Allow-Origin: *` para integración con cualquier frontend.
- **Preflight OPTIONS** — Manejado automáticamente en `index.php`.

---

## Módulos y Funcionalidades

### Refugios (`/refugios`)
CRUD completo de refugios temporales. Registra nombre, dirección, capacidad máxima, ocupación actual y estado (`activo`, `inactivo`, `lleno`).

### Familias (`/familias`)
Registro del núcleo familiar con representante, cédula, teléfono, dirección, cantidad de miembros, nivel de prioridad y refugio asignado. Incluye campo `aceptacion_habeas_data` para cumplimiento legal.

### Miembros (`/familias/miembros`)
Registro individual de cada integrante de la familia con campos específicos de vulnerabilidad: `es_embarazada`, `tiene_discapacidad`, `enfermedad_cronica`, `vulnerable`.

### Usuarios (`/usuarios`) + Login (`/login`)
Gestión de cuentas del sistema con roles. Las contraseñas se almacenan con bcrypt. El login devuelve un token JWT.

### Recursos (`/recursos`)
Inventario de insumos clasificados por tipo y unidad. El campo `cantidad_disponible` se actualiza automáticamente con cada donación y entrega.

### Donantes (`/donantes`)
Registro de personas naturales, empresas y ONGs que aportan recursos al sistema.

### Donaciones (`/donaciones`)
Registro en dos pasos: cabecera de la donación (`POST /donaciones`) y detalle de recursos (`POST /donaciones?action=agregar_detalle`). Al agregar un detalle, se incrementa automáticamente el inventario.

### Entregas (`/entregas`)
Registro de entrega de recursos a familias con descuento automático de inventario, validación de stock disponible y bloqueo por regla de 72 horas (RF-05.02).

### Gestiones (`/gestiones`)
Registro manual de movimientos de inventario y acciones administrativas.

### Priorización (`/priorizacion`)
Motor de cálculo automático de puntaje de prioridad por familia y generación de listas de despacho ordenadas.

### Reportes (`/reportes`)
Trazabilidad origen-destino de recursos y estadísticas generales del dashboard.

### Auditoría (`/auditoria`)
Log automático de acciones con usuario, acción, entidad e IP.

---

## Cumplimiento de Requerimientos Funcionales

### Módulo 1 — Gestión de Usuarios y Seguridad

| RF | Descripción | Implementación |
|---|---|---|
| RF-01.01 | Roles de usuario: Administrador, Censista, Operario, Voluntario, Auditor | `usuarios.rol` en BD. `AuthMiddleware::checkRole()` valida el rol en cada endpoint protegido. |
| RF-01.02 | Autenticación con credenciales seguras | `POST /login` → `UsuarioService::login()` con `password_verify()` + JWT en `AuthMiddleware::generateToken()`. |

### Módulo 2 — Registro Único de Damnificados (Censo)

| RF | Descripción | Implementación |
|---|---|---|
| RF-02.01 | Registrar cabeza de familia con cédula como identificador único | Campo `cedula` con restricción `UNIQUE` en tabla `familias`. `FamiliaService::createFamilia()`. |
| RF-02.02 | Asociar miembros dependientes con edad y relación | `POST /familias/miembros` → `MiembroService::createMiembro()`. Campos `edad` y `parentezco`. |
| RF-02.03 | Checkboxes de vulnerabilidad especial | Campos booleanos `es_embarazada`, `tiene_discapacidad`, `enfermedad_cronica`, `vulnerable` en tabla `miembros`. |
| RF-02.04 | Registrar ubicación actual (refugio o vivienda) | Campos `id_refugio` y `ubicacion_actual` en tabla `familias`. |
| RF-02.05 | Bloquear duplicidad de documento | Restricción `UNIQUE` en `cedula`. El `catch (PDOException)` en `FamiliaService` devuelve `409 Conflict`. |

### Módulo 3 — Gestión de Inventario y Almacén

| RF | Descripción | Implementación |
|---|---|---|
| RF-03.01 | Registrar entrada de recursos por categoría | `POST /recursos` → `RecursoService::createRecurso()`. Campo `tipo` para categoría. |
| RF-03.02 | Exigir donante en cada entrada de recursos | `POST /donaciones?action=agregar_detalle` requiere `id_donacion` válida, que a su vez tiene `id_donante` obligatorio. |
| RF-03.03 | Stock disponible en tiempo real | `cantidad_disponible` se incrementa en donaciones (`DonacionService`) y se decrementa en entregas (`EntregaService`) dentro de transacciones PDO. |
| RF-03.04 | Alerta de stock crítico | `ReporteService::getDashboardStats()` devuelve `recursos_alertas_stock` con todos los recursos con `cantidad_disponible <= 50`. |

### Módulo 4 — Motor de Priorización y Asignación

| RF | Descripción | Implementación |
|---|---|---|
| RF-04.01 | Ración de Supervivencia (mínimo 3 días por familia) | `MotorPriorizacionService::calcularRacionSupervivencia()`: 2L agua + 1.5kg alimento × miembros × 3 días. |
| RF-04.02 | Puntaje de Prioridad automático por vulnerabilidades | `MotorPriorizacionService::calcularPuntajePrioridad()`: +10 base, +20 embarazo, +20 discapacidad, +15 primera infancia (<5 años), +15 tercera edad (>65), +10 enfermedad crónica. |
| RF-04.03 | Listas de Despacho ordenadas por prioridad | `MotorPriorizacionService::generarDespachos()` → endpoint `GET /priorizacion&action=despachos`. Ordenadas con `usort()` descendente. |

### Módulo 5 — Despacho, Logística y Entregas

| RF | Descripción | Implementación |
|---|---|---|
| RF-05.01 | Búsqueda rápida por cédula | `GET //familias&action=search&q={cedula}` en `FamiliaService`. |
| RF-05.02 | Bloqueo si la familia ya recibió apoyo en menos de 72h | `EntregaService::checkUltimaEntrega()` compara fecha de última entrega. Lanza excepción con zona horaria `America/Bogota`. |
| RF-05.03 | Descuento automático de inventario al confirmar entrega | Transacción PDO en `EntregaService::createEntrega()`: INSERT en `detalle_entrega` + UPDATE `cantidad_disponible`. Rollback en caso de fallo. |

### Módulo 6 — Trazabilidad y Reportes

| RF | Descripción | Implementación |
|---|---|---|
| RF-06.01 | Reporte Origen-Destino por donante | `ReporteService::getTrazabilidadOrigenDestino()` → `GET /reportes?action=origen-destino`. JOIN entre `detalle_entrega`, `recursos`, `familias` y `donaciones`. |
| RF-06.02 | Estadísticas: % atendidos, distribución, estado censo | `ReporteService::getDashboardStats()` → `GET /reportes?action=dashboard`. Devuelve total familias, personas, entregas completadas y alertas de stock. |

---

## Requerimientos No Funcionales Cubiertos

| RNF | Descripción | Estado |
|---|---|---|
| RNF-01.01 | Backend en PHP con estructura API REST | ✅ PHP Vanilla + patrón Front Controller |
| RNF-01.02 | Base de datos relacional con integridad referencial | ✅ MySQL con Foreign Keys entre todas las tablas |
| RNF-05.01 | Cumplimiento Habeas Data (Ley 1581/2012) | ✅ Campo `aceptacion_habeas_data` en registro de familias |
| RNF-05.02 | Contraseñas encriptadas con bcrypt | ✅ `password_hash()` en creación, `password_verify()` en login |
| RNF-05.03 | Log de auditoría con timestamp, usuario e IP | ✅ `AuditoriaService::log()` registra en tabla `auditoria_logs` |

---

## Variables de Entorno Recomendadas

Para producción se recomienda mover las siguientes constantes a un archivo `.env`:

```
DB_HOST=localhost
DB_NAME=sistema_refugios
DB_USER=root
DB_PASS=

JWT_SECRET=MY_SUPER_SECRET_KEY_REFUGIOS_2026_SISTEMA
```

---