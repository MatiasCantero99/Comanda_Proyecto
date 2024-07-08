<?php

class Pedido
{
    public $id;
    public $mesa;
    public $estado;
    public $mozoAsignado;
    public $numeroPedido;
    public $rutaFoto;
    public $nombre;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta("INSERT INTO pedido ( estado, nombre, mozoAsignado, numeroPedido, mesa) VALUES ( :estado, :nombre, :mozoAsignado, :numeroPedido, :mesa)");
        $consultaInsert->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consultaInsert->bindValue(':mozoAsignado', $this->mozoAsignado, PDO::PARAM_STR);
        $consultaInsert->bindValue(':numeroPedido', $this->numeroPedido, PDO::PARAM_STR);
        $consultaInsert->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consultaInsert->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consultaInsert->execute();

    }

    public function verificarPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT numeroPedido FROM pedido WHERE mesa = :mesa AND estado != 'terminado' ");
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($resultado && isset($resultado['numeroPedido'])) {
            return $resultado['numeroPedido'];
        }
        return '';
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($numeroPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE numeroPedido = :numero");
        $consulta->bindValue(':numero', $numeroPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerNumeroPedido($mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT numeroPedido FROM pedido WHERE mesa = :mesa");
        $consulta->bindValue(':mesa', $mesa, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado['numeroPedido'];
    }

    public static function guardarFotoEnCarpeta($foto,$numeroPedido,$mesa)
    {
        $extensionImagen = pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION);
        $extensionImagen = strtolower($extensionImagen);
        
        $directorioDestino = './ImagenesPedido/2024/';
        
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }
    
        $rutaDestino = $directorioDestino . "NumeroPedido_" . $numeroPedido . "." . $extensionImagen;
        
        try {
            $foto->moveTo($rutaDestino);
            $mensaje = "La imagen se guardo correctamente en: $rutaDestino";
        } catch (Exception $e) {
            $mensaje = "Error al mover la imagen: " . $e->getMessage();
        }
        if($mensaje == "La imagen se guardo correctamente en: $rutaDestino"){
            self::guardarRutaFoto($mesa,$rutaDestino);
        }
    
        return $mensaje;
    }

    public static function guardarRutaFoto($mesa,$rutaDestino)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET rutaFoto = :rutaFoto WHERE mesa = :mesa AND estado != 'terminado' ");
        $consulta->bindValue(':mesa', $mesa, PDO::PARAM_INT);
        $consulta->bindValue(':rutaFoto', $rutaDestino, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerListaPorTipo($tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT cp.*
            FROM pedido AS p
            JOIN conceptopedido AS cp ON p.mesa = cp.mesa
            JOIN producto AS pr ON cp.idProducto = pr.id
            WHERE pr.encargado = :encargado AND cp.estado = :estado"
        );
        $consulta->bindValue(':encargado', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'pendiente', PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public static function obtenerListaPreparacion($ocupacion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM `conceptopedido` WHERE estado = :estado AND idUsuario = :id"
        );
        $consulta->bindValue(':id', $ocupacion, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'en preparacion', PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public static function obtenerListaPorCodigos($mesa,$pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT
             p.numeroPedido AS numero_pedido,
              cp.nombre AS nombre_producto,
               cp.tiempoestimado AS tiempo_producto,
                cp.estado AS estado_pedido,
                 p.tiempoestimado AS tiempo_pedido
                  FROM
                   conceptopedido cp
                    JOIN
                     pedido p ON cp.numeropedido = p.numeroPedido
                      JOIN
                       mesa m ON p.mesa = m.numero
                        WHERE
                         m.codigo = :mesa
                          AND p.numeroPedido = :pedido"
        );
        $consulta->bindValue(':mesa', $mesa, PDO::PARAM_STR);
        $consulta->bindValue(':pedido', $pedido, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }
    public static function TraeParaSocio()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT numeroPedido, tiempoestimado FROM pedido WHERE estado != :estado"
        );
        $consulta->bindValue(':estado', 'terminado', PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultado as &$fila) {
            if ($fila['tiempoestimado'] == null) {
                $fila['tiempo_Estimado'] = 'nadie a cargo del pedido';
            } else {
                $fila['tiempo_Estimado'] = $fila['tiempoestimado'] . ' minutos';
            }
            unset($fila['tiempoestimado']);
        }
        return $resultado;
    }

    public static function TraeParaMozoListo($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT cp.numeroPedido AS numero_Pedido,
            cp.estado,
            cp.nombre,
            cp.mesa
            FROM conceptopedido cp
            JOIN pedido p ON cp.numeroPedido = p.numeroPedido
            WHERE cp.estado = 'listo para servir' AND p.mozoAsignado = :id;"
        );
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public static function TraePrecioACobrar($numeroPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT SUM(p.precio) AS total_precios
            FROM conceptopedido cp
            JOIN producto p ON cp.idProducto = p.id
            WHERE cp.numeroPedido = :numeroPedido;"
        );
        $consulta->bindValue(':numeroPedido', $numeroPedido, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public static function AgregarCobro($precio,$numeroPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET cobrar = :cobrar WHERE numeroPedido = :numeroPedido"
        );
        $consulta->bindValue(':numeroPedido', $numeroPedido, PDO::PARAM_STR);
        $consulta->bindValue(':cobrar', $precio, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return 'pedido cobrado exitosamente';
    }

}