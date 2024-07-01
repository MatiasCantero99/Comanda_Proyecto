<?php

class Pedido
{
    public $id;
    public $mesa;
    public $estado;
    public $mozoAsignado;
    public $numeroPedido;
    public $rutaFoto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta("INSERT INTO pedido ( estado, mozoAsignado, numeroPedido, mesa) VALUES ( :estado, :mozoAsignado, :numeroPedido, :mesa)");
        $consultaInsert->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consultaInsert->bindValue(':mozoAsignado', $this->mozoAsignado, PDO::PARAM_STR);
        $consultaInsert->bindValue(':numeroPedido', $this->numeroPedido, PDO::PARAM_STR);
        $consultaInsert->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consultaInsert->execute();

    }

    public function verificarPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT numeroPedido FROM pedido WHERE mesa = :mesa");
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_STR);
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
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET rutaFoto = :rutaFoto WHERE mesa = :mesa");
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
            WHERE pr.encargado = :encargado AND p.estado = :estado"
        );
        $consulta->bindValue(':encargado', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'pendiente', PDO::PARAM_STR);
        $consulta->execute();
        echo "hola";
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }
}