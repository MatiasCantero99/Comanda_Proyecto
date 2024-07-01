<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './utils/AutentificadorJWT.php';
class Ingresomw
{
    public static function verificarToken(Request $request, RequestHandler $handler): Response
    {
        $token = null;
        $header = $request->getHeaderLine('Authorization');
        if($header){

            $token = trim(explode("Bearer", $header)[1]);
        }

        try {
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}