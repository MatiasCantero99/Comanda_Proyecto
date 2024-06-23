<?php
require_once './db/AccesoDatos.php';
class Autentificador
{
    public static function Ingresar($datos)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :nombre AND id = :id");
        $consulta->bindValue(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $consulta->bindValue(':id', $datos['id'], PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}