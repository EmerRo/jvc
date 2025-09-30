
-- hoy 

  1. Agregar nueva columna id_cliente a taller_cotizaciones:

  ALTER TABLE `taller_cotizaciones`
  ADD `id_cliente` INT(11) NOT NULL
  AFTER `direccion`;

  2. Actualizar registros existentes en taller_cotizaciones:

  UPDATE taller_cotizaciones tc
  JOIN clientes_taller ct ON tc.id_cliente_taller = ct.id_cliente_taller
  JOIN clientes c ON c.documento = ct.documento AND c.id_empresa = ct.id_empresa
  SET tc.id_cliente = c.id_cliente;

  3. Agregar índice para la nueva columna:

  ALTER TABLE `taller_cotizaciones`
  ADD KEY `fk_taller_cotizaciones_clientes` (`id_cliente`);

  4. Agregar constraint de foreign key:

  ALTER TABLE `taller_cotizaciones`
  ADD CONSTRAINT `fk_taller_cotizaciones_clientes`
  FOREIGN KEY (`id_cliente`) REFERENCES `clientes`(`id_cliente`)
  ON DELETE CASCADE;

  5. (OPCIONAL) Eliminar la columna antigua después de verificar:

  -- Solo ejecutar después de comprobar que todo funciona
  ALTER TABLE `taller_cotizaciones`
  DROP KEY `fk_taller_cotizaciones_clientes_taller`;

  ALTER TABLE `taller_cotizaciones`
  DROP COLUMN `id_cliente_taller`;

  6. (OPCIONAL) Eliminar tabla redundante:

  -- Solo ejecutar después de hacer backup y verificar que todo funciona
  -- DROP TABLE `clientes_taller`;

    -- 1. Eliminar la vista existente
  DROP VIEW IF EXISTS `view_taller_cotizaciones`;

  -- 2. Crear la vista actualizada
  CREATE VIEW `view_taller_cotizaciones` AS
  SELECT
      `tc`.`id_cotizacion` AS `cotizacion_id`,
      `tc`.`numero` AS `numero`,
      `tc`.`fecha` AS `fecha`,
      `tc`.`moneda` AS `moneda`,
      `tc`.`cm_tc` AS `cm_tc`,
      `tc`.`id_tido` AS `id_tido`,
      `tc`.`tipo_origen` AS `tipo_origen`,
      CASE
          WHEN `pa`.`id_preAlerta` IS NOT NULL
          THEN CONCAT(`pa`.`cliente_razon_social`,' | ',`pa`.`cliente_ruc`)
          ELSE CONCAT(`c`.`documento`,' | ',`c`.`datos`)
      END AS `documento`,
      CASE
          WHEN `pa`.`id_preAlerta` IS NOT NULL
          THEN `pa`.`cliente_razon_social`
          ELSE `c`.`datos`
      END AS `datos`,
      `tc`.`total` AS `total`,
      `tc`.`estado` AS `estado`,
      `u`.`usuario` AS `vendedor`,
      `tc`.`id_usuario` AS `usuario`,
      `tc`.`sucursal` AS `sucursal`,
      COALESCE(`pa`.`atencion_encargado`, `c`.`direccion2`) AS `atencion_encargado`
  FROM `taller_cotizaciones` `tc`
      LEFT JOIN `pre_alerta` `pa` ON `tc`.`id_prealerta` = `pa`.`id_preAlerta`
      LEFT JOIN `clientes` `c` ON `tc`.`id_cliente` = `c`.`id_cliente`
      LEFT JOIN `usuarios` `u` ON `u`.`usuario_id` = `tc`.`id_usuario`
  WHERE `tc`.`id_empresa` = '12'
      AND `tc`.`estado` <> '2'
  ORDER BY `tc`.`fecha` DESC;


CREATE TABLE `guia_equipos` (
    `id_guia_equipo` int(11) NOT NULL AUTO_INCREMENT,
    `id_guia` int(11) NOT NULL,
    `id_cotizacion_equipo` int(11) DEFAULT NULL,
    `marca` varchar(100) DEFAULT NULL,
    `equipo` varchar(100) DEFAULT NULL,
    `modelo` varchar(100) DEFAULT NULL,
    `numero_serie` varchar(100) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_guia_equipo`),
    KEY `fk_guia_equipos_guia` (`id_guia`),
    KEY `fk_guia_equipos_cotizacion_equipo` (`id_cotizacion_equipo`),
    CONSTRAINT `fk_guia_equipos_guia`
      FOREIGN KEY (`id_guia`) REFERENCES `guia_remision` (`id_guia_remision`) ON DELETE CASCADE,
    CONSTRAINT `fk_guia_equipos_cotizacion_equipo`
      FOREIGN KEY (`id_cotizacion_equipo`) REFERENCES `taller_cotizaciones_equipos` (`id_cotizacion_equipo`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

 -- 1. Agregar columna id_cotizacion_taller a la tabla guia_remision
  ALTER TABLE `guia_remision`
  ADD COLUMN `id_cotizacion_taller` int(11) DEFAULT NULL AFTER `id_cotizacion`;

  -- 2. Agregar columna guia_numero a la tabla taller_cotizaciones para guardar el número de guía
  ALTER TABLE `taller_cotizaciones`
  ADD COLUMN `guia_numero` varchar(20) DEFAULT NULL AFTER `estado`;

  
  -- Actualizar la vista view_taller_cotizaciones para incluir guia_numero
  DROP VIEW IF EXISTS `view_taller_cotizaciones`;
  CREATE VIEW `view_taller_cotizaciones` AS
  SELECT
      `tc`.`id_cotizacion` AS `cotizacion_id`,
      `tc`.`numero` AS `numero`,
      `tc`.`fecha` AS `fecha`,
      `tc`.`moneda` AS `moneda`,
      `tc`.`cm_tc` AS `cm_tc`,
      `tc`.`id_tido` AS `id_tido`,
      `tc`.`tipo_origen` AS `tipo_origen`,
      CASE WHEN `pa`.`id_preAlerta` is not null
           THEN concat(`pa`.`cliente_razon_social`,' | ',`pa`.`cliente_ruc`)
           ELSE concat(`c`.`documento`,' | ',`c`.`datos`)
      END AS `documento`,
      CASE WHEN `pa`.`id_preAlerta` is not null
           THEN `pa`.`cliente_razon_social`
           ELSE `c`.`datos`
      END AS `datos`,
      `tc`.`total` AS `total`,
      `tc`.`estado` AS `estado`,
      `tc`.`guia_numero` AS `guia_numero`,
      `u`.`usuario` AS `vendedor`,
      `tc`.`id_usuario` AS `usuario`,
      `tc`.`sucursal` AS `sucursal`,
      coalesce(`pa`.`atencion_encargado`,`c`.`direccion2`) AS `atencion_encargado`
  FROM (((`taller_cotizaciones` `tc`
           left join `pre_alerta` `pa` on(`tc`.`id_prealerta` = `pa`.`id_preAlerta`))
           left join `clientes` `c` on(`tc`.`id_cliente` = `c`.`id_cliente`))
           left join `usuarios` `u` on(`u`.`usuario_id` = `tc`.`id_usuario`))
  WHERE `tc`.`id_empresa` = '12' AND `tc`.`estado` <> '2'
  ORDER BY `tc`.`fecha` DESC;

 -- Agregar columna id_repuesto a la tabla guia_detalles
  ALTER TABLE `guia_detalles`
  ADD COLUMN `id_repuesto` int(11) DEFAULT NULL AFTER `id_producto`,
  ADD COLUMN `tipo_item` varchar(20) DEFAULT 'producto' AFTER `id_repuesto`;



-- guia 
  ALTER TABLE guia_detalles ADD COLUMN id_guia_equipo INT NULL;
    ALTER TABLE guia_detalles
  ADD CONSTRAINT fk_guia_detalles_equipo
  FOREIGN KEY (id_guia_equipo)
  REFERENCES guia_equipos(id_guia_equipo)
  ON DELETE SET NULL;

  --   -- Limpiar tabla principal de guías
  DELETE FROM guia_remision;

  -- Limpiar detalles de guías
  DELETE FROM guia_detalles;

  -- Limpiar datos de SUNAT
  DELETE FROM guia_sunat;

  -- Limpiar equipos de guías (si existe)
  DELETE FROM guia_equipos;

  -- Reiniciar contadores de AUTO_INCREMENT
  ALTER TABLE guia_remision AUTO_INCREMENT = 1;
  ALTER TABLE guia_detalles AUTO_INCREMENT = 1;
  ALTER TABLE guia_sunat AUTO_INCREMENT = 1;
  ALTER TABLE guia_equipos AUTO_INCREMENT = 1;