<?php

/**
 * DB — helper de prepared statements para mysqli.
 *
 * Objetivo: eliminar interpolación de variables en SQL sin reescribir toda
 * la conexión global. Reemplaza patrones como:
 *
 *   $sql = "SELECT * FROM ventas WHERE id_venta = '{$_POST['id']}'";
 *   $conn->query($sql);
 *
 * por:
 *
 *   DB::selectOne($conn, "SELECT * FROM ventas WHERE id_venta = ?", "i",
 *                 [DB::int($_POST['id'])]);
 *
 * Convención de tipos (igual a mysqli_stmt::bind_param):
 *   i = integer, d = double, s = string, b = blob
 *
 * Sanitizadores estáticos (int/float/str) se usan para forzar tipos antes
 * del bind, así nunca llega texto malicioso ni a la query ni al placeholder.
 */
class DB
{
    /**
     * SELECT que devuelve TODAS las filas como array asociativo.
     */
    public static function select(mysqli $conn, string $sql, string $types = '', array $params = []): array
    {
        $stmt = self::prepare($conn, $sql, $types, $params);
        if ($stmt === false) {
            return [];
        }

        $stmt->execute();
        $res = $stmt->get_result();

        if ($res === false) {
            $stmt->close();
            return [];
        }

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    /**
     * SELECT que devuelve la primera fila o null.
     */
    public static function selectOne(mysqli $conn, string $sql, string $types = '', array $params = []): ?array
    {
        $stmt = self::prepare($conn, $sql, $types, $params);
        if ($stmt === false) {
            return null;
        }

        $stmt->execute();
        $res = $stmt->get_result();

        if ($res === false) {
            $stmt->close();
            return null;
        }

        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /**
     * SELECT que devuelve un único valor escalar (primer campo de primera fila).
     */
    public static function selectValue(mysqli $conn, string $sql, string $types = '', array $params = [])
    {
        $stmt = self::prepare($conn, $sql, $types, $params);
        if ($stmt === false) {
            return null;
        }

        $stmt->execute();
        $res = $stmt->get_result();

        if ($res === false) {
            $stmt->close();
            return null;
        }

        $row = $res->fetch_array(MYSQLI_NUM);
        $stmt->close();
        return $row ? $row[0] : null;
    }

    /**
     * INSERT/UPDATE/DELETE. Devuelve true en éxito.
     * Para obtener el last insert id, usar DB::insert().
     */
    public static function execute(mysqli $conn, string $sql, string $types = '', array $params = []): bool
    {
        $stmt = self::prepare($conn, $sql, $types, $params);
        if ($stmt === false) {
            return false;
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * INSERT que devuelve el insert_id o 0 si falla.
     */
    public static function insert(mysqli $conn, string $sql, string $types = '', array $params = []): int
    {
        $stmt = self::prepare($conn, $sql, $types, $params);
        if ($stmt === false) {
            return 0;
        }
        if (!$stmt->execute()) {
            $stmt->close();
            return 0;
        }
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    // ─── Sanitizadores de tipo ────────────────────────────────────────────

    /**
     * Fuerza entero. Usa el cast nativo de PHP (intval), que conserva el prefijo
     * numérico y descarta el resto. Ej: '123abc'→123, '5 OR 1=1'→5, 'abc'→0.
     * El bind con tipo 'i' garantiza que mysqli lo envíe como entero al motor,
     * por lo que cualquier resto descartado no llega al SQL.
     */
    public static function int($v, int $default = 0): int
    {
        if ($v === null || $v === '' || is_array($v) || is_object($v)) return $default;
        if (is_bool($v)) return $v ? 1 : 0;
        return (int) $v;
    }

    /** Fuerza float. Mismo principio que int(). */
    public static function float($v, float $default = 0.0): float
    {
        if ($v === null || $v === '' || is_array($v) || is_object($v)) return $default;
        if (is_bool($v)) return $v ? 1.0 : 0.0;
        return (float) $v;
    }

    /** Trim + cast a string. Usar SIEMPRE con bind_param 's'. */
    public static function str($v, string $default = ''): string
    {
        if ($v === null) return $default;
        if (is_array($v) || is_object($v)) return $default;
        if (is_bool($v)) return $v ? '1' : '0';
        return trim((string) $v);
    }

    /**
     * Construye un IN (?, ?, ?, ...) seguro a partir de una lista de enteros.
     * Devuelve [sql_fragment, types, params] listos para concatenar al query.
     *
     * Descarta valores que no sean enteros válidos (ej: 'abc' no se incluye).
     * Los valores 0 explícitos sí se mantienen.
     *
     * Ejemplo:
     *   [$in, $t, $p] = DB::safeInInts([$_POST['ids']);
     *   DB::select($conn, "SELECT * FROM ventas WHERE id_venta IN ($in)", $t, $p);
     */
    public static function safeInInts(array $ids): array
    {
        $ints = [];
        foreach ($ids as $id) {
            // Aceptamos: enteros, strings que empiezan con dígitos (intval > 0),
            // o el string '0' literal. Descartamos basura como 'abc'.
            if (is_int($id) || is_float($id)) {
                $ints[] = (int) $id;
                continue;
            }
            if (is_string($id) || is_numeric($id)) {
                $s = trim((string) $id);
                if ($s === '0' || $s === '0.0') {
                    $ints[] = 0;
                    continue;
                }
                $n = (int) $s;
                if ($n !== 0) {  // descarta cualquier string cuyo intval sea 0
                    $ints[] = $n;
                }
            }
        }
        if (empty($ints)) {
            // IN () es inválido; devolvemos una condición imposible
            return ['NULL', '', []];
        }
        $placeholders = implode(',', array_fill(0, count($ints), '?'));
        $types        = str_repeat('i', count($ints));
        return [$placeholders, $types, $ints];
    }

    // ─── Internos ─────────────────────────────────────────────────────────

    private static function prepare(mysqli $conn, string $sql, string $types, array $params)
    {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("DB::prepare error: " . $conn->error . " | SQL: " . $sql);
            return false;
        }

        if ($types !== '' && !empty($params)) {
            if (strlen($types) !== count($params)) {
                error_log("DB::bind error: types('$types') != params(" . count($params) . ") | SQL: $sql");
                $stmt->close();
                return false;
            }
            $stmt->bind_param($types, ...$params);
        }
        return $stmt;
    }
}
