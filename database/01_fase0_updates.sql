-- FASE 0: Actualizaciones del Esquema SQL

-- 1. Seguridad / Auditoría
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Insertar roles base si no existen
INSERT IGNORE INTO roles (nombre) VALUES ('Admin'), ('Auditor'), ('Operario'), ('Voluntario'), ('Logistica');

ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS rol_id INT DEFAULT NULL;

-- Asignar rol por defecto o clave foránea si se desea
ALTER TABLE usuarios
ADD CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS auditoria_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(255) NOT NULL,
    entidad VARCHAR(100) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


-- 2. Habitabilidad y Legal
ALTER TABLE familias 
ADD COLUMN IF NOT EXISTS ubicacion_actual VARCHAR(100) DEFAULT 'Vivienda',
ADD COLUMN IF NOT EXISTS refugio_id INT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aceptacion_habeas_data BOOLEAN DEFAULT FALSE;

ALTER TABLE familias
ADD CONSTRAINT fk_familia_refugio FOREIGN KEY (refugio_id) REFERENCES refugios(id) ON DELETE SET NULL;


-- 3. Inventario
ALTER TABLE recursos 
ADD COLUMN IF NOT EXISTS categoria VARCHAR(100) DEFAULT 'Sin Categoría',
ADD COLUMN IF NOT EXISTS stock DECIMAL(10,2) DEFAULT 0.00;

ALTER TABLE donaciones 
ADD COLUMN IF NOT EXISTS origen VARCHAR(255) DEFAULT 'Desconocido',
ADD COLUMN IF NOT EXISTS categoria VARCHAR(100) DEFAULT 'Sin Categoría';


-- 4. Priorización y Entregas
-- Relacionar atómicamente la tabla entregas con recursos mediante un detalle si es muchos a muchos
-- o añadiendo los campos específicos en la entrega si es 1 a 1.
CREATE TABLE IF NOT EXISTS entrega_recursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entrega_id INT NOT NULL,
    recurso_id INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (entrega_id) REFERENCES entregas(id) ON DELETE CASCADE,
    FOREIGN KEY (recurso_id) REFERENCES recursos(id) ON DELETE CASCADE
);

-- Si se necesita asegurar que las entregas trackean estado para bloqueos:
ALTER TABLE entregas
ADD COLUMN IF NOT EXISTS estado VARCHAR(50) DEFAULT 'Completada',
ADD COLUMN IF NOT EXISTS fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP;
