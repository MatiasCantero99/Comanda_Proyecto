<?php
require_once './models/Encuesta.php';

class EncuestaController
{

    public static function ValidarEncuesta($encuesta){
        $response = "";
        if (!Validador::ValidarSTR($encuesta['codigoMesa'])){
            $response .= "El codigo de mesa no es alfanumerico. ";
        }
        if (!Validador::ValidarSTR($encuesta['codigoPedido'])){
            $response .= "El codigo de pedido no es alfanumerico. ";
        }
        if (!Validador::ValidarInt($encuesta['mesa'])){
            $response .= "La encuesta de la mesa no es numerica. ";
        }
        if (!Validador::ValidarInt($encuesta['restaurante'])){
            $response .= "La encuesta del restaurante no es numerica. ";
        }
        if (!Validador::ValidarInt($encuesta['mozo'])){
            $response .= "La encuesta del mozo no es numerica. ";
        }
        if (!Validador::ValidarInt($encuesta['cocinero'])){
            $response .= "La encuesta del cocinero no es numerica. ";
        }
        if (!Validador::ValidarSTR($encuesta['comentario']) || !Validador::validarLongitud($encuesta['comentario'])){
            $response .= "El comentario no es texto o la longitud supera los 66 caracteres. ";
        }
        if($encuesta['mesa'] > 10 || $encuesta['restaurante'] > 10 || $encuesta['mozo'] > 10 || $encuesta['cocinero'] > 10){
            $response .= "Algun parametro supera los 10 ";
        }
        return $response;
    }
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        // Creamos el producto
        $encuesta = new Encuesta();
        $encuesta->codigoMesa = $parametros["codigoMesa"];
        $encuesta->codigoPedido = $parametros['codigoPedido'];
        $encuesta->mesa = $parametros['mesa'];
        $encuesta->restaurante = $parametros['restaurante'];
        $encuesta->mozo = $parametros['mozo'];
        $encuesta->cocinero = $parametros['cocinero'];
        $encuesta->comentario = $parametros['comentario'];
        $encuesta->crearEncuesta();

        $payload = json_encode(array("mensaje" => "Encuesta creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejores($request, $response, $args)
    {
        $mensaje = Encuesta::traerMejores();
        $payload = json_encode(array("Encuestas" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
