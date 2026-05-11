# Fase 3: Gestión Avanzada de Inventario (Completada)

## Objetivos Alcanzados
En esta fase se implementaron reglas de negocio específicas y complejas referidas a la capacidad máxima física del almacén y el sistema de alertas tempranas sobre el inventario.

1. **Ampliación de DTOs**:
   - `CreateDonacionDTO`, `UpdateDonacionDTO`: Se añadieron los campos `origen` y `categoria`.
   - `CreateRecursoDTO`, `UpdateRecursoDTO`: Se añadieron los campos `categoria` y `stock`.

2. **Límite de Capacidad (20.000 kg)**:
   - Modificamos `DonacionService.php` (`addDetalleDonacion`).
   - Se añadió un control transaccional donde se calcula dinámicamente:
     `SELECT SUM(COALESCE(stock, cantidad_disponible, 0)) FROM recursos`.
   - Si la donación entrante excede las 20 toneladas en total, se lanza un `Exception` que dispara el rollback transaccional y devuelve error `400` advirtiendo del límite de bodega.

3. **Lógica de Stock Real y Alertas Críticas**:
   - Se añadió la lógica en `RecursoService.php`:
     - `getAlertasStock()`: Verifica los recursos que presentan un escenario riesgoso (stock o cantidad\_disponible menor a 10). Manda un `message` de atención o salud.
     - `getStockReal()`: Retorna consolidado monetario/kilogramos o unidades de recursos disponibles con total global de almacén.

4. **Endpoints Implementados**:
   - Se actualizó el `RecursoController` y `index.php` para inyectar un argumento de `$action`.
   - `GET /api/recursos?action=stock-real` -> Retorna la suma total de elementos guardados.
   - `GET /api/recursos?action=alertas` -> Lista de recursos con stock menor a 10.

## Validación
Todo el código pasó la revisión mediante la terminal, asegurando la compatibilidad con las tablas y base de datos (PDO) modificada en la Fase 0.

---
**Siguiente Paso**: Revisión para la confirmación e inicio de la Fase 4 (Sistema de Entregas).