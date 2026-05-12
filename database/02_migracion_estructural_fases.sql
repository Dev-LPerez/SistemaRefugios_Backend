-- ============================================================
-- SCRIPT DE MIGRACIÓN: ACOPLAMIENTO DE ESQUEMA A FASES 1-6
-- ============================================================

-- 1. Estandarización de nombres (Cambiamos las tablas a PLURAL para que coincidan con la lógica de los Services)
RENAME TABLE familia TO familias;
RENAME TABLE donacion TO donaciones;
RENAME TABLE usuario TO usuarios;

-- 2. Expansión de la tabla `familias` (Prevención de duplicados, trazabilidad y marco legal)
ALTER TABLE familias
    CHANGE COLUMN id_refugio refugio_id INT(11) NULL,
    ADD COLUMN cedula VARCHAR(50) NULL AFTER id_familia,
    ADD COLUMN ubicacion_actual VARCHAR(50) DEFAULT 'Refugio' AFTER refugio_id,
    ADD COLUMN aceptacion_habeas_data TINYINT(1) DEFAULT 0 AFTER ubicacion_actual;

-- 3. Expansión de la tabla `donaciones` (Trazabilidad Origen-Destino)
ALTER TABLE donaciones
    ADD COLUMN origen VARCHAR(100) NULL AFTER id_donante,
    ADD COLUMN categoria VARCHAR(100) NULL AFTER origen;

-- 4. Expansión de límite de la tabla `recursos` (Gestión dual de inventarios y límites físicos)
ALTER TABLE recursos
    ADD COLUMN stock INT(11) DEFAULT 0 AFTER cantidad_disponible,
    ADD COLUMN categoria VARCHAR(100) NULL AFTER tipo;

-- 5. Creación de la Entidad de `auditoria_logs` (Core de Seguridad de la Fase 1)
CREATE TABLE IF NOT EXISTS auditoria_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT(11) NOT NULL,
    accion VARCHAR(100) NOT NULL,
    entidad VARCHAR(150) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45) NOT NULL
);

-- 6. Índices de Alto Rendimiento (Para la Fase 6 y Búsquedas Ágiles de Terreno Fase 5)
CREATE INDEX idx_familia_cedula ON familias(cedula);
CREATE INDEX idx_detalle_entrega_familia ON detalle_entrega(id_familia);
CREATE INDEX idx_detalle_entrega_fecha ON detalle_entrega(fecha);
