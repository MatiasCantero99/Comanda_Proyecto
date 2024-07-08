<?php

class Logger
{
    public $id;
    public $method;
    public $uri;
    public $params;
    public $message;
    public $ocupacion;
    public $nombre;

    public function crearLogger()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (method,uri,params, message, ocupacion, usuario) VALUES (:method, :uri, :params, :message, :ocupacion, :usuario)");
        $consulta->bindValue(':method', $this->method, PDO::PARAM_STR);
        $consulta->bindValue(':uri', $this->uri, PDO::PARAM_STR);
        $consulta->bindValue(':params', $this->params, PDO::PARAM_STR);
        $consulta->bindValue(':message', $this->message, PDO::PARAM_STR);
        $consulta->bindValue(':ocupacion', $this->ocupacion, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function TraerPorSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logs WHERE ocupacion = :sector");
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }

    public static function TraerPorGrupoSector()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ocupacion, usuario, COUNT(*) AS cantidad_operaciones FROM logs GROUP BY ocupacion, usuario ORDER BY ocupacion, usuario;");
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }

    public static function TraerPorEmpleado($empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT usuario, timestamp, uri FROM logs WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $empleado, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }
}