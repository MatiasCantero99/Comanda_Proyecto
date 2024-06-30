<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './controllers/UsuarioController.php';
class Logginmw
{
    public function loginsSeteados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        if(isset($parametros['id']) && isset($parametros['clave'])){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "No estan todas los parametros usados"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function loginValidados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();

        $mensaje = UsuarioController::ValidarUsuarioLogin($parametros);
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