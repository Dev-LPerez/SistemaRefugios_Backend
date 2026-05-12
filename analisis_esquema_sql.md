# Análisis de Discrepancias: Lógica de Código vs Esquema SQL Original

Tras analizar exhaustivamente el diagrama relacional de tu base de datos original (`usuario`, `detalle_gestion`, `donante`, `donacion`, `refugio`, `recursos`, `detalle_entrega`, `familia`, `miembros`, `detalle_donacion`) y contrastarlo con el código desarrollado desde la **Fase 1 hasta la Fase 6**, tienes toda la razón: **el código mutó asumiendo que los requerimientos de la Fase 0 (Migraciones Estructurales) ya estaban aplicados, pero la base de datos se mantuvo con la estructura clásica**.

A continuación, detallo exactamente qué nos hace falta a nivel de base de datos para que el sistema funcione al 100% sin errores de *Unknown column* o *Table doesn't exist*.

## 1. Discrepancias de Nomenclatura (Singular vs Plural)
En el diagrama SQL, las tablas principales están en **singular** (`familia`, `donacion`, `usuario`), pero en el código PHP, en múltiples servicios usamos **plural** (`familias`, `donaciones`, `usuarios`).
- **Impacto:** Consultas vitales como el recuento de familias, priorización y reportes fallarán afirmando que la tabla `familias` no existe.

## 2. Columnas Faltantes por Nuevos Requerimientos
Añadimos reglas de negocio avanzadas (Habeas Data, límites de stock, seguimiento de cédulas) que requieren campos que **no existen** en tu esquema visual original:

### Tabla `familia`
- Se implementó validación por **Cédula** (evitar duplicados). Falla porque no existe el campo `cedula`.
- Se introdujo `ubicacion_actual` ('Refugio' o 'Vivienda') y `aceptacion_habeas_data`.
- Tu diagrama dice `id_refugio`, pero algunos controladores intentan insertar `refugio_id`.

### Tabla `donacion`
- Se requiere llevar trazabilidad y categorización. Faltan los campos `origen` y `categoria`.

### Tabla `recursos`
- El nuevo motor de inventario usa un control dual entre lo que entra y lo disponible. Falta el campo `stock` y opcionalmente `categoria`.

## 3. Tablas Inexistentes
- **`auditoria_logs`**: La **Fase 1** inyecta un interceptor global que guarda qué usuario hizo qué acción, a qué hora y con qué IP. Ninguna de estas peticiones `POST`/`PUT` o `DELETE` va a funcionar ahora mismo porque al intentar insertar el log, la base de datos arrojará la excepción de que `auditoria_logs` no existe.
- **Índices de Optimización**: La Fase 6 implementa búsquedas veloces en terreno. Si no existen los índices, buscar por cédula tomará demasiado tiempo cuando haya 50,000 registros.

---

## Solución Recomendada (Migración Práctica)

Diseñé un script SQL que ejecuta exactamente los parches ("ALTER TABLE", "RENAME" y "CREATE") para acoplar la BD de tu diagrama a nuestro código PHP nuevo, sin borrar tus datos actuales.

Este archivo ha sido creado en tu proyecto en la ruta: `database/02_migracion_estructural_fases.sql`.

> **Nota Adicional:** Haré una corrección quirúrgica en `EntregaService.php` para asegurarnos que la palabra `familia` sea plural (`familias`) y tener perfecta homogeneidad en todas las SQL construidas.