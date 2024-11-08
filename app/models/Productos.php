<?php

class Productos
{
    public $id;
    public $nombre;
    public $encargado;
    public $stock;
    public $precio;
    public $fechaIngreso;
    public $fechaVencimiento;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO producto (nombre,stock,precio,fechaIngreso,fechaVencimiento, encargado) VALUES (:nombre, :stock, :precio, :fechaIngreso, :fechaVencimiento, :encargado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':fechaIngreso', $this->fechaIngreso, PDO::PARAM_STR);
        $consulta->bindValue(':fechaVencimiento', $this->fechaVencimiento, PDO::PARAM_STR);
        $consulta->bindValue(':encargado', $this->encargado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Productos');
    }

    public static function obtenerMasVendido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
            p.nombre AS nombre_producto,
            COUNT(cp.idProducto) AS cantidad_vendida
            FROM 
            conceptopedido cp
            JOIN 
            producto p ON cp.idProducto = p.id
            GROUP BY 
            cp.idProducto, p.nombre
            ORDER BY 
            cantidad_vendida DESC");
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultados;
    }

    public static function obtenerProducto($nombreProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombreProducto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Productos');
    }

    public static function DescargarCSV()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT id,nombre,encargado,precio FROM producto");
        $consulta->execute();
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultados;
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}