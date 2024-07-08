<?php

class Encuesta
{
    public $id;
    public $codigoMesa;
    public $codigoPedido;
    public $mesa;
    public $restaurante;
    public $mozo;
    public $cocinero;
    public $comentario;
    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta (codigoMesa,codigoPedido,mesa,restaurante, mozo, cocinero, comentario) VALUES (:codigoMesa, :codigoPedido, :mesa, :restaurante, :mozo, :cocinero, :comentario)");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consulta->bindValue(':restaurante', $this->restaurante, PDO::PARAM_INT);
        $consulta->bindValue(':mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':cocinero', $this->cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function traerMejores(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT comentario,
                    (mesa + cocinero + restaurante + mozo) AS puntaje_total
             FROM encuesta
             ORDER BY puntaje_total DESC
             LIMIT 3;"
        );
        $consulta->execute();
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $resultados;
    }
}