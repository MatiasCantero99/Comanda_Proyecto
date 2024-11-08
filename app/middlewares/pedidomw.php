<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './controllers/PedidoController.php';
require_once './utils/AutentificadorJWT.php';
require_once './utils/Validador.php';
class Pedidomw
{
    public function pedidoSeteados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        if(isset($parametros['pedido']) && isset($parametros['mesa']) && isset($parametros['nombreCliente'])){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "No estan todas los parametros usados"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function pedidoValidados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = PedidoController::ValidarPedido($parametros,$datos);
        if($mensaje == ""){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array('mensaje' => $mensaje));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function listaValidados(Request $request, RequestHandler $handler): Response
    {   
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = PedidoController::ValidarLista($datos);
        if($mensaje == ""){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array('mensaje' => $mensaje));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cambiarSeteados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        if(isset($parametros['codigo']) && isset($parametros['tiempo'])){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "No estan todas los parametros usados"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cambiarValidados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = PedidoController::ValidarCambio($parametros,$datos);
        if($mensaje == ""){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array('mensaje' => $mensaje));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function validarSocio(Request $request, RequestHandler $handler,): Response
    {   
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $resultado = Validador::ValidarTipoEspecifico($datos->ocupacion,'socio');
        if($resultado){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Usuario no es socio'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function validarMozo(Request $request, RequestHandler $handler,): Response
    {   
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $resultado = Validador::ValidarTipoEspecifico($datos->ocupacion,'mozo');
        if($resultado){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Usuario no es mozo'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function listoSeteados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        if(isset($parametros['codigo'])){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "No estan todas los parametros usados"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function listoValidados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = PedidoController::ValidarListo($parametros,$datos);
        if($mensaje == ""){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array('mensaje' => $mensaje));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

}