# Documentación Fase 2: Adaptación del Censo y Preparación Offline

## Objetivo
Robustecer el módulo de registro de damnificados (familias), prevenir duplicados al ingresar al sistema e implementar el soporte para PWA que permite la sincronización masiva de datos recolectados en el terreno sin conexión.

## Acciones Realizadas

### 1. Actualización de capa DTO (Data Transfer Object)
- Se actualizaron las clases de persistencia de Familias (`CreateFamiliaDTO.php` y `UpdateFamiliaDTO.php`).
- Se introdujo validación estricta y mapeo de los nuevos campos agregados en Fase 0:
  - `cedula`: Primordial para evitar censar a la misma persona 2 veces.
  - `ubicacion_actual`: Soporta si están alojados en un refugio ('Refugio') o pasaron a lugar privado ('Vivienda').
  - `aceptacion_habeas_data`: Flag legal sobre responsabilidad de datos recolectados por voluntarios en zona de desastre.

### 2. Prevención de Duplicados en Capa de Servicio
- Dentro de `FamiliaService.php` se creó el método protector `checkDuplicidad($cedula)` para consultar ágilmente si un DNI/Cédula ya existe en la base de datos de damnificados.
- El manejador base de posteos ahora rechaza las subidas que caigan en conflictos de duplicidad.

### 3. Logística Offline y Sincronización Masiva ACID
- Se escribió el manejador matriz `syncMasivo(array $datosFamilias)` en `FamiliaService.php`.
- Está envuelto de inicio a fin en una única transacción de Base de Datos (`BEGIN TRANSACTION` y `COMMIT`). Si durante el parseo de cientos de insertos uno hace colapsar la integridad de la base, un manejador `rollBack()` retrocede todas las inserciones salvaguardando la base de datos de escenarios inconclusos o de inconsistencias.
- Cuenta con un contador iterativo que emite un reporte inteligente informando el número de registros guardados, así como el número de filas *"omitidas"* (sea por falta de Habeas Data o por ser ya existentes en DB).

### 4. Controlador Expuesto
- Se actualizó el manejador principal del enrutador en `index.php` para inyectar inteligentemente un wildcard de `$action` sobre la clase `FamiliaController`.
- La ruta expuesta es entonces: `POST /api/familias?action=sync` lista para consumir un gran JSON enviado desde el App móvil o las PWA de terreno de los voluntarios.

## Arquitectura Resultante
- Endpoint Estándar: `POST /api/familias`
- Endpoint Masivo Offline: `POST /api/familias?action=sync`
- En caso de falla general: Revierte sin destruir Data previa al último commit de Base de Datos.

---

## Próximos Pasos
Finalizada la recolección ágil y segura de la data del censo, tenemos la visibilidad requerida para pasar a la **Fase 3: Lógica Avanzada de Inventario y Almacén**.