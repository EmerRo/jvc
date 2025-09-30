-- Agregar columna foto_perfil a la tabla usuarios
-- Ejecutar este script en la base de datos factura_jvc1

ALTER TABLE `usuarios`
ADD COLUMN `foto_perfil` varchar(255) DEFAULT NULL AFTER `rotativo`;

-- Opcional: Comentar qué representa esta columna
ALTER TABLE `usuarios`
MODIFY COLUMN `foto_perfil` varchar(255) DEFAULT NULL COMMENT 'Ruta de la foto de perfil del usuario';