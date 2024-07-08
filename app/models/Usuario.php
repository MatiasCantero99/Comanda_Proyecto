<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $fechaAlta;
    public $fechaBaja;
    public $ocupacion;
    public $mesaOcupada;
    public $edad;
    public $nombre;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, nombre, clave,fechaBaja,ocupacion,fechaAlta,mesaOcupada,edad) VALUES (:usuario, :nombre, :clave, :fechaBaja, :ocupacion, :fechaAlta, :mesaOcupada, :edad)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $edad = (int)$this->edad;
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->bindValue(':ocupacion', $this->ocupacion, PDO::PARAM_STR);
        $consulta->bindValue(':edad', $edad, PDO::PARAM_INT);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':mesaOcupada', $this->mesaOcupada, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function PDF()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, ocupacion, edad FROM usuarios");
        $consulta->execute();

        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
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

    public static function asignarMesa($id,$mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET mesaOcupada = :mesa WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':mesa',$mesa );
        $consulta->execute();
    }
}