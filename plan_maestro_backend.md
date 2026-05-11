# Plan Maestro de Desarrollo e Implementación Backend: Sistema de Refugios y Ayuda Humanitaria

Este documento unifica los requerimientos técnicos y la hoja de ruta paso a paso. Establece cómo construir de forma secuencial la Arquitectura MVC (Controllers, Services, Entity, DTO) para cubrir la lógica de negocio, seguridad e integración del sistema.

---

## FASE 0: Preparación del Entorno y Estructura de Base de Datos
*Antes de programar las fases y tocar la lógica de negocio, debemos asegurar que el ecosistema y las tablas están listas reflejando los nuevos requerimientos.*

### 0.1 Preparación de Dependencias
1. **Instalar Gestor de Paquetes:** Si no usas Composer, inicializarlo en la raíz (`composer init`).
2. **Instalar JSON Web Token:** Instalar paquete requerido para la Fase 1: `composer require firebase/php-jwt`.
3. **Configuración DB:** Asegurarte que `src/config/database.php` usa PDO o MySQLi manejando correctamente el modo de errores para las transacciones (ACID). (Ej. `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`).

### 0.2 Actualización del Esquema SQL
- [ ] **Seguridad/Auditoría:** Crear tabla `auditoria_logs` (id, usuario_id, accion, entidad, fecha, ip). Añadir `rol_id` a la tabla `usuarios`.
- [ ] **Habitabilidad y Legal:** Añadir a tabla `familias`: `ubicacion_actual` (Refugio/Vivienda), `refugio_id` (Nulo si está en vivienda), y `aceptacion_habeas_data` (Boolean).
- [ ] **Inventario:** Modificar tabla `recursos` para categoría y stock. Modificar `donaciones` añadiendo `origen` y `categoria`.
- [ ] **Priorización y Entregas:** Añadir campos a `entregas` para relacionarlo atómicamente con los recursos despachados.

---

## FASE 1: Core de Seguridad, Roles y Auditoría
**Objetivo:** Proteger los endpoints existentes, establecer control de acceso basado en roles y habilitar la trazabilidad de las acciones.

**Tareas de Desarrollo:**
- [ ] **Tarea 1.1: Autenticación.** Implementar sistema de inicio de sesión utilizando JSON Web Tokens (JWT). Encriptación de contraseñas de las entidades `Usuario` con `bcrypt`. *(RF-01.02, RNF-05.02)*
- [ ] **Tarea 1.2: Control de Accesos (Middlewares).** Crear middlewares para proteger las rutas según los 5 roles estipulados. *(RF-01.01)*
- [ ] **Tarea 1.3: Sistema de Auditoría.** Crear entidad `LogAuditoria`. Implementar un interceptor (u Observer) que registre qué usuario hace cada acción, fecha/hora y dirección IP. *(RNF-05.03)*

**Detalle Técnico:**
- **Rutas:** `POST /api/usuarios/login`, `POST /api/usuarios/logout`, `GET /api/auditoria` (solo para `Auditor` o `Admin`).
- **Código:** Middleware `AuthMiddleware::checkToken()` y `AuthMiddleware::checkRole()`. Servicio `AuditoriaService->log()`.

**Guía de Implementación Paso a Paso:**
1. Modificar `CreateUsuarioDTO` y `UpdateUsuarioDTO` para que encripten contraseñas automáticamente usando `password_hash()`.
2. Crear `AuthMiddleware.php` en una nueva carpeta `src/middlewares/`.
3. Crear endpoint `POST /api/usuarios/login` en `UsuarioController` para emitir tokens.
4. Crear la entidad/servicio `LogAuditoria` e inyectarla en todos los controladores que modifiquen datos (POST, PUT, DELETE).

---

## FASE 2: Adaptación del Censo y Preparación Offline
**Objetivo:** Robustecer el módulo de registro de damnificados, prevenir duplicados e implementar soporte PWA.

**Tareas de Desarrollo:**
- [ ] **Tarea 2.1: Endpoint de Sincronización.** Crear endpoint `POST /familias/sync` para recibir arreglos masivos sin conexión. *(RNF-02.01, RNF-02.02)*
- [ ] **Tarea 2.2: Validación de Duplicidad.** Lógica en `FamiliaService` para validar DNI previo a sincronizar. *(RF-02.05)*
- [ ] **Tarea 2.3: Optimización Estructural.** Calcular tamaño del núcleo y validar vulnerabilidades en DTOs. *(RF-02.02, RF-02.03)*
- [ ] **Tarea 2.4: Logística y Legalidad.** Integrar campos de ubicación e flag Habeas Data en DB y DTOs. *(RF-02.04, RNF-05.01)*

**Detalle Técnico:**
- **Rutas:** `POST /api/familias/sync` (carga masiva array JSON), `POST /api/familias` (normal), `GET /api/familias/{cedula}`.
- **Código:** Servicio `FamiliaService->syncMasivo(array $datos)`. Todo bajo `BEGIN TRANSACTION`.

**Guía de Implementación Paso a Paso:**
1. **Actualizar DTOs:** Integrar a `CreateFamiliaDTO` las reglas booleanas de Habeas Data, embarazos y discapacidades.
2. **Lógica de Duplicidad:** En `FamiliaService` añadir método que verifique existencia del DNI.
3. **El Sincronizador:** Crear método en `FamiliaController` `syncOffline` que parsee un gran JSON y ejecute inserciones bajo una transacción PDO.

---

## FASE 3: Lógica Avanzada de Inventario y Almacén
**Objetivo:** Motor de inventario en tiempo real con restricciones físicas.

**Tareas de Desarrollo:**
- [ ] **Tarea 3.1: Control y Cálculo de Stock.** Actualizar disponibilidad automáticamente basándose en Entradas/Salidas. *(RF-03.03)*
- [ ] **Tarea 3.2: Límite de Capacidad (20.000 kg).** Revertir transacciones si el ingreso a almacén supera el límite físico. *(RF-03.04)*
- [ ] **Tarea 3.3: Alertas de Desabastecimiento.** Consultar umbrales mínimos. *(RF-03.04)*
- [ ] **Tarea 3.4: Categorización y Origen.** Modificar DTOs obligando al registro del donante y categoría. *(RF-03.01, RF-03.02)*

**Detalle Técnico:**
- **Rutas:** `GET /api/recursos/stock-real`, `GET /api/recursos/alertas`.
- **Código:** Query de agregación `SUM(peso)`. Validador `RecursoService::checkCapacidad()`.

**Guía de Implementación Paso a Paso:**
1. **Regla de 20Toneladas:** En `DonacionService`, previo a registrar entrada, invocar una DB query (`SUM`) del inventario y si excede 20,000 kg aplicar un `throw new Exception`.
2. Crear endpoints de estado y de alertas de seguridad del almacén. Modificar `RecursoService` para llevar el conteo real.

---

## FASE 4: Construcción del Motor de Priorización
**Objetivo:** Desarrollar el algoritmo central que empareja necesidades humanitarias con logística.

**Tareas de Desarrollo:**
- [ ] **Tarea 4.1: Algoritmo "Ración de Supervivencia".** Calcular qué necesita cada familia por 3 días. *(RF-04.01)*
- [ ] **Tarea 4.2: Algoritmo de Puntaje de Prioridad.** Baremar familias según integrantes bajo vulnerabilidad. *(RF-04.02)*
- [ ] **Tarea 4.3: Generador de Listas de Despacho.** Endpoint que cruce prioridad vs inventario y devuelva armados de kits. *(RF-04.03)*

**Detalle Técnico:**
- **Rutas:** `GET /api/priorizacion/despachos`, `POST /api/priorizacion/calcular`.
- **Código:** Crear `CalculadoraKitService` y `PriorizacionService`.

**Guía de Implementación Paso a Paso:**
1. **Nuevo Servicio Central:** Crear `MotorPriorizacionService.php` en una nueva carpeta.
2. Programar la función que evalúa 1 Familia + N Miembros = Puntaje Entero.
3. Programar el calculador calórico de 3 días = *Ración de Supervivencia*.
4. Exponer mediante un endpoint `GET /priorizacion/despachos` a los operarios logísticos.

---

## FASE 5: Transaccionalidad de Entregas y Bloqueos
**Objetivo:** Proveer endpoints ágiles y seguros para entrega en terreno.

**Tareas de Desarrollo:**
- [ ] **Tarea 5.1: Regla de Restricción de 3 Días.** Bloquear la entrega si la familia recibió apoyo hace menos de 72 hrs. *(RF-05.02)*
- [ ] **Tarea 5.2: Transacciones ACID en Despachos.** Envolver el registro de la entrega y el descuento al inventario en la misma Transacción DB. *(RF-05.03)*
- [ ] **Tarea 5.3: Búsqueda Ágil para Terreno.** Endpoint rápido (`/familias/search`) optimizado con índices. *(RF-05.01, RNF-03.02)*

**Detalle Técnico:**
- **Rutas:** `POST /api/entregas`, `GET /api/familias/search?q=NUM_CEDULA_O_QR`.
- **Código:** Bloque `try { checkUltimaEntrega(); descontar(); insert(); commit(); } catch { rollback(); }`.

**Guía de Implementación Paso a Paso:**
1. Escribir método `checkUltimaEntrega($familia_id)` en `EntregaService`. Si el diff de fecha_creacion y hoy es menor a 3 días (72 hrs), abortar.
2. Proteger `EntregaController::create` con `BEGIN TRANSACTION`. Descontar inventario (Update a tabla de recursos) y registrar entrega en el mismo bloque try-catch.
3. Programar el buscador veloz `/familias/search` (usando índices SQL de DNI).

---

## FASE 6: Reportes, Trazabilidad y Optimización Final
**Objetivo:** Explotar la información para proveer visibilidad gráfica y auditoría.

**Tareas de Desarrollo:**
- [ ] **Tarea 6.1: Trazabilidad "Origen - Destino".** Trazar desde origen donante hasta familia receptora. *(RF-06.01)*
- [ ] **Tarea 6.2: Endpoints de Dashboard Estadístico.** JSONs pre-procesados para dashboards. *(RF-06.02)*
- [ ] **Tarea 6.3: Pruebas de Carga y Optimización.** Sembrado de 50k individuos y revisión de índices en tablas. *(RNF-03.01)*

**Detalle Técnico:**
- **Rutas:** `GET /api/reportes/origen-destino`, `GET /api/reportes/dashboard`.
- **Código:** Consultas SQL avanzadas y `DashboardResponseDTO`.

**Guía de Implementación Paso a Paso:**
1. Añadir endpoints de reportes JSON puros en los controladores o crear un `ReporteController` separado.
2. Construir la consulta compleja SQL `JOIN` entre Donante -> Inventario -> Entregas -> Familiar.
3. Hacer pruebas de carga y documentar para frontend (Posman / Swagger).

---

## Metodología de Integración / Git Flow sugerida
Se recomienda abordar **una fase a la vez**, comenzando estrictamente por la **Fase 0** (Scripts de BD). Posteriormente, se sugiere avanzar en orden desarrollando el servicio, probando exhaustivamente la lógica en Postman, asegurando transaccionalidad, y procediendo recién entonces a la siguiente fase.