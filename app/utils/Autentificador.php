<?php
require_once './db/AccesoDatos.php';
class Autentificador
{
    public static function Ingresar($datos)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $datos['id'], PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}