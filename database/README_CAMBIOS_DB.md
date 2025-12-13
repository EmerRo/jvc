# 📊 CAMBIOS EN BASE DE DATOS - GESTIÓN DE STOCK

## 📅 Fecha: 13 de Diciembre 2024

---

## 🎯 RESUMEN DE CAMBIOS

Se implementó un sistema completo de gestión de stock para **Productos** y **Repuestos** con las siguientes funcionalidades:

1. ✅ Aumentar Stock (con costo de compra)
2. ✅ Disminuir Stock
3. ✅ Traslado Entre Almacenes
4. ✅ Historial de Movimientos

---

## 🗄️ CAMBIOS EN TABLAS

### **1. Tabla: `historial_stock`**

**Cambio:** Se agregó la columna `costo_compra`

```sql
ALTER TABLE historial_stock 
ADD COLUMN costo_compra DECIMAL(10,2) NULL 
AFTER cantidad;
```

**Estructura Final:**
```
+------------------+--------------------------+------+-----+---------+----------------+
| Field            | Type                     | Null | Key | Default | Extra          |
+------------------+--------------------------+------+-----+---------+----------------+
| id               | int                      | NO   | PRI | NULL    | auto_increment |
| id_producto      | int                      | NO   | MUL | NULL    |                |
| tipo_movimiento  | enum('INGRESO','EGRESO') | NO   |     | NULL    |                |
| cantidad         | int                      | NO   |     | NULL    |                |
| costo_compra     | decimal(10,2)            | YES  |     | NULL    | ← NUEVO        |
| fecha_movimiento | datetime                 | NO   |     | NULL    |                |
| usuario          | varchar(100)             | YES  |     | NULL    |                |
| observaciones    | text                     | YES  |     | NULL    |                |
+------------------+--------------------------+------+-----+---------+----------------+
```

**Propósito:**
- Registrar el costo específico de cada compra/ingreso
- Permite calcular costo promedio ponderado (futuro)
- Mantiene historial de precios de compra

---

### **2. Tabla: `historial_stock_repuestos` (NUEVA)**

**Cambio:** Se creó una tabla nueva para repuestos

```sql
CREATE TABLE historial_stock_repuestos (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_repuesto INT NOT NULL,
    tipo_movimiento ENUM('INGRESO','EGRESO') NOT NULL,
    cantidad INT NOT NULL,
    costo_compra DECIMAL(10,2) NULL,
    fecha_movimiento DATETIME NOT NULL,
    usuario VARCHAR(100) NULL,
    observaciones TEXT NULL,
    INDEX idx_repuesto (id_repuesto),
    INDEX idx_fecha (fecha_movimiento)
);
```

**Propósito:**
- Separar historial de productos y repuestos
- Misma estructura que `historial_stock`
- Facilita consultas y reportes específicos

---

## 📝 TIPOS DE MOVIMIENTOS

### **INGRESO**
- Aumentar Stock manualmente
- Traslado (almacén destino)
- Compras

### **EGRESO**
- Disminuir Stock manualmente
- Traslado (almacén origen)
- Ventas/Salidas

---

## 🔍 CONSULTAS ÚTILES

### Ver últimos movimientos de productos:
```sql
SELECT 
    h.*,
    p.nombre,
    p.codigo
FROM historial_stock h
JOIN productos p ON h.id_producto = p.id_producto
ORDER BY h.fecha_movimiento DESC
LIMIT 20;
```

### Ver últimos movimientos de repuestos:
```sql
SELECT 
    h.*,
    r.nombre,
    r.codigo
FROM historial_stock_repuestos h
JOIN repuestos r ON h.id_repuesto = r.id_repuesto
ORDER BY h.fecha_movimiento DESC
LIMIT 20;
```

### Ver traslados entre almacenes:
```sql
SELECT 
    h.*,
    p.nombre,
    p.codigo
FROM historial_stock h
JOIN productos p ON h.id_producto = p.id_producto
WHERE h.observaciones LIKE '%Traslado%'
ORDER BY h.fecha_movimiento DESC;
```

### Calcular costo promedio de un producto:
```sql
SELECT 
    id_producto,
    AVG(costo_compra) as costo_promedio,
    MIN(costo_compra) as costo_minimo,
    MAX(costo_compra) as costo_maximo,
    COUNT(*) as total_compras
FROM historial_stock
WHERE tipo_movimiento = 'INGRESO'
  AND costo_compra IS NOT NULL
  AND id_producto = 123
GROUP BY id_producto;
```

---

## ⚠️ IMPORTANTE

### **Backup Antes de Aplicar:**
```bash
mysqldump -uroot factura_jvc > backup_antes_stock_$(date +%Y%m%d).sql
```

### **Aplicar Migraciones:**
```bash
mysql -uroot factura_jvc < database/migrations_2024_12_13_stock_management.sql
```

### **Verificar Cambios:**
```sql
-- Ver estructura de historial_stock
DESCRIBE historial_stock;

-- Ver estructura de historial_stock_repuestos
DESCRIBE historial_stock_repuestos;

-- Contar registros
SELECT COUNT(*) FROM historial_stock;
SELECT COUNT(*) FROM historial_stock_repuestos;
```

---

## 📊 IMPACTO EN EL SISTEMA

### **Archivos Modificados:**

**Backend:**
- `app/http/controllers/ProductosController.php`
  - `aumentarStock()` - Actualizado
  - `disminuirStock()` - Nuevo
  - `trasladoAlmacenes()` - Nuevo
  - `obtenerHistorialStock()` - Actualizado

- `app/http/controllers/RepuestosController.php`
  - `aumentarStock()` - Actualizado
  - `disminuirStock()` - Nuevo
  - `trasladoAlmacenes()` - Nuevo
  - `obtenerHistorialStock()` - Actualizado

**Rutas:**
- `routes/ajax2.php` - Agregadas rutas para stock

**Frontend:**
- `resources/views/fragment-views/cliente/almacen-productos.php`
- `resources/views/fragment-views/cliente/modals/product-modal-aumentar-stock.php`
- `resources/views/fragment-views/cliente/modals/product-modal-disminuir-stock.php`
- `resources/views/fragment-views/cliente/modals/product-modal-traslado-almacenes.php`
- `resources/views/fragment-views/cliente/modals/product-modal-historial-stock.php`

---

## 🚀 PRÓXIMAS MEJORAS (OPCIONAL)

1. **Costo Promedio Ponderado:**
   - Actualizar automáticamente el campo `costo` en productos/repuestos
   - Ver documento: `EXPLICACION-COSTO-PROMEDIO-PONDERADO.md`

2. **Reportes:**
   - Reporte de movimientos por fecha
   - Reporte de traslados entre almacenes
   - Valorización de inventario

3. **Alertas:**
   - Stock mínimo
   - Productos sin movimiento
   - Diferencias de inventario

---

## 📞 SOPORTE

Si tienes dudas sobre estos cambios, revisa:
- `EXPLICACION-COSTO-PROMEDIO-PONDERADO.md`
- `database/migrations_2024_12_13_stock_management.sql`

---

**Última actualización:** 13 de Diciembre 2024
