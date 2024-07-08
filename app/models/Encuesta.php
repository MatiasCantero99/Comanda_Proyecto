<?php

class Encuesta
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
}