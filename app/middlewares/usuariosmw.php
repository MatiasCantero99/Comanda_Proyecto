<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './controllers/UsuarioController.php';
require_once './utils/AutentificadorJWT.php';
class Usuariosmw
{
    public function usuarioSeteados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        if(isset($parametros['usuario']) && isset($parametros['clave']) && isset($parametros['ocupacion']) && isset($parametros['edad']) && isset($parametros['nombre'])){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "No estan todas los parametros usados"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function usuarioValidados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = UsuarioController::ValidarUsuario($parametros,$datos);
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