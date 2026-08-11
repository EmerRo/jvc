-- Secuencia global unica para informes, ordenada por la fecha e ID originales.
SELECT GET_LOCK('jvc_informes_numero', 30) INTO @informe_lock_adquirido;

SET @informes_requieren_renumeracion = (
    SELECT IF(
        EXISTS(SELECT 1 FROM informes WHERE numero <= 0)
        OR EXISTS(
            SELECT 1
            FROM informes
            GROUP BY numero
            HAVING COUNT(*) > 1
        ),
        1,
        0
    )
);

CREATE TEMPORARY TABLE informes_numeracion_global AS
SELECT
    id_informe,
    ROW_NUMBER() OVER (ORDER BY fecha_creacion, id_informe) AS numero_nuevo
FROM informes;

ALTER TABLE informes_numeracion_global
    ADD PRIMARY KEY (id_informe);

UPDATE informes AS i
INNER JOIN informes_numeracion_global AS n ON n.id_informe = i.id_informe
SET i.numero = n.numero_nuevo
WHERE @informes_requieren_renumeracion = 1;

DROP TEMPORARY TABLE informes_numeracion_global;

SET @indice_informes_numero_existe = (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'informes'
      AND index_name = 'uq_informes_numero'
);

SET @sql_indice_informes = IF(
    @indice_informes_numero_existe = 0,
    'ALTER TABLE informes ADD UNIQUE INDEX uq_informes_numero (numero)',
    'SELECT 1'
);
PREPARE stmt_indice_informes FROM @sql_indice_informes;
EXECUTE stmt_indice_informes;
DEALLOCATE PREPARE stmt_indice_informes;

SELECT RELEASE_LOCK('jvc_informes_numero') INTO @informe_lock_liberado;
