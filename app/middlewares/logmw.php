<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './controllers/LoggerController.php';
require_once './utils/AutentificadorJWT.php';
class Logmw
{
    public static function LogOperacion(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        if($header){
            $token = trim(explode("Bearer", $header)[1]);
            $datos = AutentificadorJWT::ObtenerData($token);
        }
        else{
            $datos = new stdClass;
            $datos->ocupacion = "cliente";
            $datos->nombre = "-";
        }
        $method = $request->getMethod();
        $uri = (string)$request->getUri();
        $params = json_encode($request->getParsedBody());
        LoggerController::CargarUno($method, $uri, $params, 'Solicitud recibida',$datos->ocupacion,$datos->nombre);
        
        $response = $handler->handle($request);
        return $response;
    }
}