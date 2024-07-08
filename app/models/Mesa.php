<?php

class Mesa
{
    public $id;
    public $numero;
    public $estado;
    public $fechaIngreso;
    public $fechaBaja;
    public $codigo;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (numero,fechaIngreso,fechaBaja,estado, codigo) VALUES (:numero, :fechaIngreso, :fechaBaja, :estado, :codigo)");
        $consulta->bindValue(':numero', $this->numero, PDO::PARAM_STR);
        $consulta->bindValue(':fechaIngreso', $this->fechaIngreso, PDO::PARAM_STR);
        $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT numero, estado  FROM mesa");
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public static function obtenerMesa($numeroMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT numero, estado  FROM mesa");
        $consulta->bindValue(':numero', $numeroMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarEstado($numeroMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesa SET estado = :estado WHERE numero = :mesa");
        $consulta->bindValue(':mesa', $numeroMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', 'con cliente comiendo', PDO::PARAM_STR);
        $consulta->execute();

    }

    public static function modificarCobrar($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesa SET estado = :estado WHERE codigo = :mesa");
        $consulta->bindValue(':mesa', $codigo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', 'cerrada', PDO::PARAM_STR);
        $consulta->execute();

    }

    public static function modificarCerrar($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesa SET estado = :estado WHERE codigo = :mesa");
        $consulta->bindValue(':mesa', $codigo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', 'con cliente pagando', PDO::PARAM_STR);
        $consulta->execute();

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