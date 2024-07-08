<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './controllers/EncuestaController.php';
require_once './utils/AutentificadorJWT.php';
class Encuestamw
{
    public function encuestaSeteados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        if(isset($parametros['codigoMesa']) && isset($parametros['codigoPedido']) && isset($parametros['mesa']) && isset($parametros['restaurante']) && isset($parametros['mozo']) && isset($parametros['cocinero']) && isset($parametros['comentario'])){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "No estan todas los parametros usados"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function encuestaValidados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        $mensaje = EncuestaController::ValidarEncuesta($parametros);
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