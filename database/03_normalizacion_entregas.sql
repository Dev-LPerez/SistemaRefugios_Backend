-- 1. Crear la tabla de cabecera 'entregas'
CREATE TABLE `entregas` (
  `id_entrega` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `estado` enum('pendiente','entregado','cancelado') DEFAULT 'entregado',
  `id_familia` int DEFAULT NULL,
  PRIMARY KEY (`id_entrega`),
  FOREIGN KEY (`id_familia`) REFERENCES `familias` (`id_familia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Migrar los datos existentes de 'detalle_entrega' a la nueva tabla 'entregas'
INSERT INTO `entregas` (`id_entrega`, `fecha`, `estado`, `id_familia`)
SELECT `id_entrega`, `fecha`, `estado`, `id_familia` FROM `detalle_entrega`;

-- 3. Eliminar llaves foráneas y columnas obsoletas en 'detalle_entrega'
ALTER TABLE `detalle_entrega`
  DROP FOREIGN KEY `detalle_entrega_ibfk_1`;

ALTER TABLE `detalle_entrega`
  DROP INDEX `detalle_entrega_ibfk_1`,
  DROP INDEX `idx_detalle_entrega_fecha`;

ALTER TABLE `detalle_entrega`
  DROP COLUMN `fecha`,
  DROP COLUMN `estado`,
  DROP COLUMN `id_familia`;

-- 4. Renombrar la primary key a 'id_detalle' y restaurar la configuración
ALTER TABLE `detalle_entrega`
  CHANGE COLUMN `id_entrega` `id_detalle` int NOT NULL AUTO_INCREMENT,
  ADD COLUMN `id_entrega` int NOT NULL AFTER `id_detalle`;

-- 5. Para no perder la correlación durante la migración (ya que antes id_entrega era mapeado 1:1), llenamos el nuevo campo id_entrega
SET SQL_SAFE_UPDATES = 0;
UPDATE `detalle_entrega` SET `id_entrega` = `id_detalle`;
SET SQL_SAFE_UPDATES = 1;

-- 6. Agregar la llave foránea conectando el detalle a la cabecera
ALTER TABLE `detalle_entrega`
  ADD CONSTRAINT `fk_detalle_entrega_entregas` FOREIGN KEY (`id_entrega`) REFERENCES `entregas` (`id_entrega`) ON DELETE CASCADE ON UPDATE CASCADE;