<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './controllers/PedidoController.php';
require_once './utils/AutentificadorJWT.php';
class Fotomw
{
    public function fotoSeteados(Request $request, RequestHandler $handler): Response
    {   
        $foto = $request->getUploadedFiles();
        $parametros = $request->getParsedBody();
        if(isset($foto['foto']) && isset($parametros['mesa'])){
            $response = $handler->handle($request);
        }
        else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "No estan todas los parametros usados"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fotoValidados(Request $request, RequestHandler $handler): Response
    {   
        $parametros = $request->getParsedBody();
        $foto = $request->getUploadedFiles();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = PedidoController::ValidarFoto($foto,$datos,$parametros);
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