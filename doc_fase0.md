# Documentación Fase 0: Preparación del Entorno y Estructura de Base de Datos

## Objetivo
Preparar el ecosistema del proyecto y establecer las bases a nivel de dependencias y base de datos antes de programar la lógica de negocio y las fases posteriores.

## Acciones Realizadas

### 1. Preparación de Dependencias
- Se inicializó **Composer** (`composer init`) en la raíz del proyecto para la correcta gestión de los paquetes y namespaces.
- Se instaló la biblioteca **JSON Web Token** (`firebase/php-jwt`) mediante el comando `composer require firebase/php-jwt`. Esta biblioteca será la base para la autenticación y el control de accesos que se implementará en la Fase 1.
- Se generaron los archivos `composer.json`, `composer.lock` y la carpeta `vendor/`.

### 2. Configuración de Base de Datos
- Se inspeccionó el archivo `src/config/database.php`.
- Se verificó que las configuraciones base de **PDO** incluyen el manejo correcto de errores y soporte para transacciones ACID:
  ```php
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ```
  Esto garantizará que los `BEGIN TRANSACTION` y los bloques `try-catch` funcionen de manera óptima y atómica.

### 3. Actualización del Esquema SQL
Se creó un script de migración inicial en el archivo `database/01_fase0_updates.sql` para reflejar los nuevos requerimientos del plan maestro:

*   **Seguridad y Auditoría:**
    *   Se creó la tabla `roles` y se introdujeron roles por defecto.
    *   Se añadió la columna `rol_id` a la tabla `usuarios` y se estableció la FK.
    *   Se creó la tabla `auditoria_logs` (`id`, `usuario_id`, `accion`, `entidad`, `fecha`, `ip`) conectada a `usuarios`.
*   **Habitabilidad y Responsabilidad Legal:**
    *   Se añadieron las columnas `ubicacion_actual`, `refugio_id` y `aceptacion_habeas_data` a la tabla `familias`.
    *   Se agregó la relación foránea (FK) con la tabla `refugios`.
*   **Gestión de Inventario:**
    *   La tabla `recursos` recibió las columnas `categoria` y `stock`.
    *   La tabla `donaciones` ahora cuenta con `origen` y `categoria`.
*   **Delivery y Priorización:**
    *   Se diseñó una tabla intermedia `entrega_recursos` para enlazar atómicamente cada entrega con un set de recursos y su respectiva `cantidad`.
    *   Se aseguraron los campos `estado` y `fecha_creacion` en `entregas` para soportar las reglas de bloqueo temporal (regla de los 3 días).

## Próximos Pasos
Una vez aprobada esta base estructural por el usuario, el proyecto está listo para pasar a la **Fase 1: Core de Seguridad, Roles y Auditoría**.
