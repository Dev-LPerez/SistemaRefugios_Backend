# Documentación de Avance: Sistema de Refugios y Ayuda Humanitaria (Backend)

## 1. Arquitectura y Configuración Base (Completado)
El sistema ha sido estructurado bajo buenas prácticas de ingeniería de software para garantizar seguridad y mantenibilidad:

* **Arquitectura MVC y Capas:** El proyecto es una API RESTful construida en PHP puro, dividida lógicamente en Controladores (peticiones HTTP), Servicios (lógica de negocio), Entidades y DTOs (Data Transfer Objects para validar datos de entrada).
* **Front Controller y Enrutamiento Limpio:** Todas las peticiones convergen en un único punto de entrada (`index.php`), el cual gracias a reglas de reescritura en el archivo `.htaccess`, permite tener URLs limpias (ej. `/refugios/1`).
* **Transaccionalidad (ACID):** La conexión a la base de datos se maneja mediante PDO con manejo de errores configurado (`ERRMODE_EXCEPTION`), lo que garantiza que operaciones críticas (como registrar entregas y descontar inventario) sean atómicas.

## 2. Fases Completadas

### Fase 1: Core de Seguridad, Roles y Auditoría
Se protegió la integridad del sistema y los datos:

* **Autenticación JWT:** Se implementó la librería `firebase/php-jwt` para generar y validar tokens de acceso seguros en cada petición HTTP.
* **Encriptación de contraseñas:** Los DTOs de creación y actualización de usuarios (`CreateUsuarioDTO`, `UpdateUsuarioDTO`) usan nativamente `bcrypt` (`password_hash`) para almacenar credenciales de forma segura.
* **Control de Accesos (Middleware):** Se programó un `AuthMiddleware` que verifica el rol del usuario (Admin, Operario, Voluntario, etc.) antes de permitir el consumo de un endpoint.
* **Auditoría Transversal:** Cada mutación en la base de datos (POST, PUT, DELETE) es interceptada y registrada automáticamente (`auditoria_logs`) con el usuario responsable, la acción realizada y la dirección IP.

### Fase 2: Censo de Damnificados y Soporte Offline
Se adaptó el sistema para soportar las condiciones en zonas de desastre:

* **Sincronización Masiva Offline:** Se habilitó el endpoint `POST /familias?action=sync` que recibe grandes lotes de familias desde dispositivos móviles sin internet. Utiliza envoltorios `BEGIN TRANSACTION` y `COMMIT` para guardar todo el bloque atómicamente.
* **Prevención de Duplicados:** `FamiliaService` incluye el método protector `checkDuplicidad($cedula)` para asegurar que no se cense a la misma persona dos veces.
* **Responsabilidad Legal:** Se añadió el campo booleano `aceptacion_habeas_data` para cumplimiento de la normativa de recolección de datos.

### Fase 3 y Base de la Fase 5: Inventario, Almacén y Entregas
* **Límites Físicos del Almacén:** `DonacionService` intercepta las donaciones entrantes y verifica dinámicamente si superarían las 20 toneladas (20,000 kg). Si el límite es excedido, cancela la transacción (`rollBack()`) y lanza un error.
* **Alertas Críticas:** Se implementó `getAlertasStock()` para identificar productos con inventario riesgoso (menor a 10 unidades).
* **Despachos ACID (Fase 5 parcial):** Ya existe el módulo de entregas (`EntregaService`). Este descuenta de manera automática y transaccional los recursos entregados a una familia, verificando primero que haya stock suficiente.

## 3. Fases Pendientes
De acuerdo a los Requisitos (RF) y al archivo `plan_desarrollo_backend.md`, estas son las tareas exactas que faltan para dar el proyecto por finalizado:

### Fase 4 - Motor de Priorización (Totalmente Pendiente):
* Falta crear el algoritmo de "Puntaje de Prioridad" que asigne puntos (ej. +10 embarazada, +5 niño) para decidir a qué familias despacharles primero.
* Falta la lógica para calcular la "Ración de Supervivencia" (garantizar calorías para 3 días por el número de miembros).
* Falta exponer el endpoint `GET /api/priorizacion/despachos`.

### Fase 5 - Restricciones Logísticas y Búsquedas (Parcialmente Pendiente):
* **Bloqueo de 72 horas:** Aunque las entregas se guardan y descuentan inventario, actualmente en `EntregaService.php` no se valida la regla de los 3 días (RF-05.02). Falta programar el chequeo que bloquea la entrega si la familia ya recibió ayuda hace menos de 72 horas.
* **Búsqueda Ágil:** Se debe crear el endpoint `/familias/search` optimizado para escanear documentos de identidad o QR en terreno rápidamente.

### Fase 6 - Reportes y Trazabilidad (Totalmente Pendiente):
* Faltan las consultas complejas SQL (JOINs avanzados) para el reporte de "Origen y Destino" (RF-06.01), que trace una donación desde la entidad donante hasta las familias exactas que lo recibieron.
* Faltan los endpoints `/reportes/dashboard` que proveerán la metadata (JSON pre-procesado) a las gráficas del frontend.