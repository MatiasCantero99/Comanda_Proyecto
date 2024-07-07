<?php

class ConceptoPedido
{
    public $id;
    public $idProducto;
    public $mesa;
    public $nombre;
    public $numeroPedido;
    public $numeroPedidoIndividual;
    public $estado;

    public function crearConceptoPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $producto = $this->obtenerIDProducto();
        if ($producto) {
            $productoId = $producto['id'];
            
            $consultaInsert = $objAccesoDatos->prepararConsulta("INSERT INTO conceptopedido (idProducto, estado, numeroPedido, mesa, numeroPedidoIndividual, nombre) VALUES (:idProducto, :estado, :numeroPedido, :mesa, :numeroPedidoIndividual, :nombre)");
            $consultaInsert->bindValue(':idProducto', $productoId, PDO::PARAM_INT);
            $consultaInsert->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consultaInsert->bindValue(':numeroPedido', $this->numeroPedido, PDO::PARAM_STR);
            $consultaInsert->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consultaInsert->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
            $consultaInsert->bindValue(':numeroPedidoIndividual', $this->numeroPedidoIndividual, PDO::PARAM_STR);
            $consultaInsert->execute();

            return 'Pedido creado con exito';
        }
        else{
            return 'Nombre del Producto no encontrado';
        }
    }

    public function obtenerIDProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM producto WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);;
    }
    public function obtenerIDMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM mesa WHERE numero = :mesa");
        $consulta->bindValue(':mesa', $this->nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
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

    public static function guardarFotoEnCarpeta($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public static function cambiarPedido($numeroPedido,$tiempo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE conceptopedido SET estado = :estado, tiempoestimado = :tiempoestimado WHERE numeroPedidoIndividual = :numeroPedido");
        $consulta->bindValue(':numeroPedido', $numeroPedido, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'en preparacion', PDO::PARAM_STR);
        $consulta->bindValue(':tiempoestimado', (int)$tiempo, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function cambiarTiempo($numeroPedido,$tiempo)
    {
        $numeroPedidoOriginal = self::traeNumeroPedido($numeroPedido);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET estado = :estado, tiempoestimado = :nuevoTiempoEstimado  WHERE numeroPedido = :numeroPedido AND (tiempoestimado < :compararTiempo OR tiempoestimado IS NULL) ");
        $consulta->bindValue(':numeroPedido', $numeroPedidoOriginal, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'en preparacion', PDO::PARAM_STR);
        $consulta->bindValue(':nuevoTiempoEstimado', (int)$tiempo, PDO::PARAM_INT);
        $consulta->bindValue(':compararTiempo', (int)$tiempo, PDO::PARAM_INT);
        echo "\n hola3sad";
        $consulta->execute();
        // try {
        //     $consulta->execute();
        //     echo "\n hola2";
        // } catch (PDOException $e) {
        //     echo 'Error en la consulta: ' . $e->getMessage();
        // }
    }

    public static function traeNumeroPedido($numeroPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  numeroPedido FROM conceptopedido WHERE numeroPedidoIndividual = :numeroPedido");
        $consulta->bindValue(':numeroPedido', $numeroPedido, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado['numeroPedido'];
    }
}