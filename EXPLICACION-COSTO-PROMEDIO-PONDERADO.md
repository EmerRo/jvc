# 📊 EXPLICACIÓN: COSTO PROMEDIO PONDERADO

## 🎯 ¿QUÉ ES EL COSTO PROMEDIO PONDERADO?

Es un método contable para calcular el costo real de tu inventario cuando compras el mismo producto a diferentes precios.

---

## 📚 EJEMPLO PASO A PASO (MUY FÁCIL)

Imagina que vendes **ACEITE DE MOTOR 20W-50**

### **SITUACIÓN INICIAL:**
- Stock actual: **0 unidades**
- Costo actual: **S/ 0.00**

---

### **📦 COMPRA 1 - Enero 2024**

**Compras:** 100 unidades a **S/ 25.00** cada una

**Cálculo:**
```
Stock nuevo = 0 + 100 = 100 unidades
Costo total = (0 × 0) + (100 × 25) = S/ 2,500.00
Costo promedio = 2,500 ÷ 100 = S/ 25.00
```

**Resultado:**
- Stock: 100 unidades
- Costo promedio: **S/ 25.00**

---

### **📦 COMPRA 2 - Marzo 2024**

**Situación actual:**
- Stock: 100 unidades
- Costo: S/ 25.00

**Compras:** 50 unidades a **S/ 30.00** cada una (subió el precio)

**Cálculo:**
```
Stock nuevo = 100 + 50 = 150 unidades

Valor del stock anterior = 100 × 25.00 = S/ 2,500.00
Valor de la compra nueva = 50 × 30.00 = S/ 1,500.00
Valor total = 2,500 + 1,500 = S/ 4,000.00

Costo promedio = 4,000 ÷ 150 = S/ 26.67
```

**Resultado:**
- Stock: 150 unidades
- Costo promedio: **S/ 26.67** ⬆️ (subió)

---

### **📦 COMPRA 3 - Mayo 2024**

**Situación actual:**
- Stock: 150 unidades
- Costo: S/ 26.67

**Compras:** 80 unidades a **S/ 24.00** cada una (bajó el precio)

**Cálculo:**
```
Stock nuevo = 150 + 80 = 230 unidades

Valor del stock anterior = 150 × 26.67 = S/ 4,000.50
Valor de la compra nueva = 80 × 24.00 = S/ 1,920.00
Valor total = 4,000.50 + 1,920 = S/ 5,920.50

Costo promedio = 5,920.50 ÷ 230 = S/ 25.74
```

**Resultado:**
- Stock: 230 unidades
- Costo promedio: **S/ 25.74** ⬇️ (bajó un poco)

---

## 🧮 FÓRMULA GENERAL

```
Nuevo Costo Promedio = (Valor Stock Actual + Valor Compra Nueva) ÷ (Stock Actual + Cantidad Nueva)

Donde:
- Valor Stock Actual = Stock Actual × Costo Actual
- Valor Compra Nueva = Cantidad Nueva × Costo de Compra
```

---

## 💻 CÓDIGO PHP PARA IMPLEMENTAR

```php
public function aumentarStock()
{
    $respuesta = ["res" => false];

    try {
        $producto_id = $_POST['producto_id'];
        $cantidad = intval($_POST['cantidad']);
        $costo_compra = floatval($_POST['costo_compra']);
        $observaciones = $_POST['observaciones'] ?? null;
        $fecha_actual = date('Y-m-d H:i:s');

        // 1. OBTENER DATOS ACTUALES DEL PRODUCTO
        $sql = "SELECT cantidad, costo FROM productos WHERE id_producto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $producto_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();
        
        $stock_actual = floatval($producto['cantidad']);
        $costo_actual = floatval($producto['costo']);

        // 2. CALCULAR NUEVO COSTO PROMEDIO PONDERADO
        $valor_stock_actual = $stock_actual * $costo_actual;
        $valor_compra_nueva = $cantidad * $costo_compra;
        $valor_total = $valor_stock_actual + $valor_compra_nueva;
        
        $stock_nuevo = $stock_actual + $cantidad;
        $costo_promedio = $valor_total / $stock_nuevo;

        // 3. ACTUALIZAR PRODUCTO CON NUEVO STOCK Y COSTO PROMEDIO
        $sql = "UPDATE productos SET 
                cantidad = ?, 
                costo = ?,
                fecha_ultimo_ingreso = ?
                WHERE id_producto = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ddsi', $stock_nuevo, $costo_promedio, $fecha_actual, $producto_id);

        if ($stmt->execute()) {
            // 4. REGISTRAR EN HISTORIAL
            $sql_historial = "INSERT INTO historial_stock 
                         (id_producto, tipo_movimiento, cantidad, costo_compra, fecha_movimiento, usuario, observaciones) 
                         VALUES (?, 'INGRESO', ?, ?, ?, ?, ?)";

            $stmt_hist = $this->conexion->prepare($sql_historial);
            $usuario = $_SESSION['usuario'] ?? 'Administrador';
            $stmt_hist->bind_param('iidsss', $producto_id, $cantidad, $costo_compra, $fecha_actual, $usuario, $observaciones);
            $stmt_hist->execute();

            $respuesta["res"] = true;
            $respuesta["nuevo_costo"] = round($costo_promedio, 2);
        }

    } catch (Exception $e) {
        $respuesta["error"] = $e->getMessage();
    }

    return json_encode($respuesta);
}
```

---

## ✅ VENTAJAS DEL COSTO PROMEDIO PONDERADO

### 1. **Refleja el Costo Real**
Si tienes 100 unidades a S/25 y compras 50 a S/30, tu costo real no es ni S/25 ni S/30, es S/26.67

### 2. **Cálculo de Ganancia Correcto**
```
Ejemplo de VENTA:
- Vendes 1 unidad a S/ 40.00
- Costo promedio: S/ 26.67
- Ganancia real: S/ 40.00 - S/ 26.67 = S/ 13.33

Si usaras el costo antiguo (S/25):
- Ganancia incorrecta: S/ 40.00 - S/ 25.00 = S/ 15.00 ❌
```

### 3. **Valorización Correcta del Inventario**
```
Stock: 230 unidades
Costo promedio: S/ 25.74
Valor del inventario: 230 × 25.74 = S/ 5,920.20 ✅

Si usaras el último costo (S/24):
Valor incorrecto: 230 × 24 = S/ 5,520.00 ❌
Diferencia: S/ 400.20 (error en tu contabilidad)
```

### 4. **Cumple con Normas Contables**
Es uno de los métodos aceptados por SUNAT y normas internacionales (NIC 2)

---

## 📊 COMPARACIÓN DE MÉTODOS

| Método | Ventaja | Desventaja |
|--------|---------|------------|
| **Sin actualizar** | Simple | Costo desactualizado |
| **Último costo** | Fácil | No refleja inventario real |
| **Costo promedio ponderado** | Preciso y correcto | Requiere cálculo |

---

## 🎓 RESUMEN PARA ENTENDER FÁCIL

**Piensa en esto:**

Si tienes una bolsa con:
- 10 caramelos que costaron S/1 cada uno = S/10 total
- Agregas 5 caramelos que costaron S/2 cada uno = S/10 total

**¿Cuánto cuesta cada caramelo ahora?**

```
Total gastado: S/10 + S/10 = S/20
Total caramelos: 10 + 5 = 15
Costo por caramelo: S/20 ÷ 15 = S/1.33
```

**Eso es el costo promedio ponderado** 🎯

---

## 🚀 CUÁNDO IMPLEMENTARLO

**Implementa esto SI:**
- ✅ Compras el mismo producto a diferentes precios
- ✅ Quieres saber tu ganancia real
- ✅ Necesitas valorizar tu inventario correctamente
- ✅ Quieres cumplir con normas contables

**NO lo necesitas SI:**
- ❌ Cada producto tiene un precio fijo que nunca cambia
- ❌ Solo vendes servicios (no productos físicos)
- ❌ Tu negocio es muy pequeño y no necesitas precisión

---

## 📝 NOTAS IMPORTANTES

1. **El costo de compra en el historial NO cambia** - es un registro histórico
2. **El costo del producto SÍ cambia** - se actualiza con el promedio
3. **Cada ingreso recalcula el promedio** - es dinámico
4. **Las ventas NO afectan el costo** - solo los ingresos

---

## 🤔 PREGUNTAS FRECUENTES

### ¿Qué pasa si vendo productos?
El costo promedio NO cambia cuando vendes, solo cambia cuando compras/ingresas.

### ¿Y si el stock llega a 0?
Cuando vuelvas a comprar, ese será tu nuevo costo (porque no hay stock anterior).

### ¿Puedo ver el historial de costos?
Sí, en la tabla `historial_stock` tienes el `costo_compra` de cada ingreso.

### ¿Es obligatorio usar este método?
No es obligatorio, pero es el más recomendado para negocios que manejan inventario.

---

## 📞 CONCLUSIÓN

El **Costo Promedio Ponderado** es como sacar el promedio de tus calificaciones:

```
Matemática: 15 (peso 3 créditos)
Historia: 18 (peso 2 créditos)

Promedio simple: (15 + 18) ÷ 2 = 16.5 ❌ (incorrecto)
Promedio ponderado: (15×3 + 18×2) ÷ (3+2) = 16.2 ✅ (correcto)
```

**Lo mismo pasa con tu inventario:** no puedes promediar precios sin considerar las cantidades.

---

**¿Listo para implementarlo? Solo dime y lo agrego al código.** 🚀
