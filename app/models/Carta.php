<?php
// app/models/Carta.php

class Carta
{
    private $id;
    private $id_cliente;
    private $id_usuario;
    private $tipo;
    private $titulo;
    private $contenido;
    private $header_image;
    private $footer_image;
    private $estado;
    private $fecha_creacion;
    private $fecha_modificacion;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Getters y setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getContenido()
    {
        return $this->contenido;
    }

    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }

    public function getHeaderImage()
    {
        return $this->header_image;
    }

    public function setHeaderImage($header_image)
    {
        $this->header_image = $header_image;
    }

    public function getFooterImage()
    {
        return $this->footer_image;
    }

    public function setFooterImage($footer_image)
    {
        $this->footer_image = $footer_image;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    // Métodos para obtener URLs de imágenes
    public function getHeaderImageUrl()
    {
        if ($this->header_image) {
            return $this->header_image;
        }
        
        // Obtener la imagen de la plantilla
        $template = new CartaTemplate();
        if ($template->obtenerTemplateActual()) {
            return $template->getHeaderImageUrl();
        }
        
        return URL::to('public/img/garantia/header.png'); // Imagen por defecto
    }

    public function getFooterImageUrl()
    {
        if ($this->footer_image) {
            return $this->footer_image;
        }
        
        // Obtener la imagen de la plantilla
        $template = new CartaTemplate();
        if ($template->obtenerTemplateActual()) {
            return $template->getFooterImageUrl();
        }
        
        return URL::to('public/img/garantia/footer.png'); // Imagen por defecto
    }

    // Métodos CRUD
    public function obtenerCarta($id)
    {
        $sql = "SELECT * FROM cartas WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($fila = $result->fetch_assoc()) {
            $this->id = $fila['id'];
            $this->id_cliente = $fila['id_cliente'];
            $this->id_usuario = $fila['id_usuario'];
            $this->tipo = $fila['tipo'];
            $this->titulo = $fila['titulo'];
            $this->contenido = $fila['contenido'];
            $this->header_image = $fila['header_image'];
            $this->footer_image = $fila['footer_image'];
            $this->estado = $fila['estado'];
            $this->fecha_creacion = $fila['fecha_creacion'];
            $this->fecha_modificacion = $fila['fecha_modificacion'];
            return true;
        }
        return false;
    }

 public function insertarCarta()
{
    try {
        $sql = "INSERT INTO cartas (id_cliente, id_usuario, tipo, titulo, contenido, header_image, footer_image, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $this->conectar->error);
        }
        
        $stmt->bind_param("iissssss", 
            $this->id_cliente, 
            $this->id_usuario, 
            $this->tipo, 
            $this->titulo, 
            $this->contenido, 
            $this->header_image, 
            $this->footer_image, 
            $this->estado
        );
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        
        if ($result) {
            $this->id = $this->conectar->insert_id;
        }
        
        return $result;
    } catch (Exception $e) {
        // Registrar el error para depuración
        error_log("Error en insertarCarta: " . $e->getMessage());
        throw $e; // Re-lanzar la excepción para que sea capturada por el controlador
    }
}

    public function actualizarCarta()
    {
        $sql = "UPDATE cartas 
                SET id_cliente = ?, id_usuario = ?, tipo = ?, titulo = ?, 
                    contenido = ?, header_image = ?, footer_image = ?, 
                    estado = ? 
                WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("iissssssi", 
            $this->id_cliente, 
            $this->id_usuario, 
            $this->tipo, 
            $this->titulo, 
            $this->contenido, 
            $this->header_image, 
            $this->footer_image, 
            $this->estado, 
            $this->id
        );
        return $stmt->execute();
    }

    public function eliminarCarta($id)
    {
        $sql = "DELETE FROM cartas WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

  public function listarCartas($filtro = null, $tipo_busqueda = null)
{
    try {
        // Usar los nombres correctos de las columnas según tu esquema de base de datos
        $sql = "SELECT c.*, 
                cl.datos AS cliente_nombre, 
                cl.documento AS cliente_documento,
                cl.direccion AS cliente_direccion,
                u.usuario AS usuario_nombre
                FROM cartas c
                LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
                LEFT JOIN usuarios u ON c.id_usuario = u.usuario_id
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Aplicar filtros si existen
        if ($filtro && $tipo_busqueda) {
            if ($tipo_busqueda === 'titulo') {
                $sql .= " AND c.titulo LIKE ?";
                $params[] = "%$filtro%";
                $types .= "s";
            } elseif ($tipo_busqueda === 'tipo') {
                $sql .= " AND c.tipo LIKE ?";
                $params[] = "%$filtro%";
                $types .= "s";
            } elseif ($tipo_busqueda === 'cliente') {
                $sql .= " AND cl.datos LIKE ?"; // Usar datos en lugar de nombre
                $params[] = "%$filtro%";
                $types .= "s";
            }
        }
        
        $sql .= " ORDER BY c.fecha_creacion DESC";
        
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $this->conectar->error);
        }
        
        // Bind parameters if any
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cartas = [];
        while ($row = $result->fetch_assoc()) {
            $cartas[] = $row;
        }
        
        return $cartas;
    } catch (Exception $e) {
        error_log("Error en listarCartas: " . $e->getMessage());
        return [];
    }
}

    public function obtenerTiposCartas()
    {
        $sql = "SELECT DISTINCT tipo FROM cartas WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo";
        $result = $this->conectar->query($sql);
        
        $tipos = [];
        while ($fila = $result->fetch_assoc()) {
            $tipos[] = $fila['tipo'];
        }
        
        return $tipos;
    }
}