<?php
require_once './models/Logger.php';
require_once './controllers/ArchivosController.php';

class LoggerController
{
    public static function CargarUno($method,$uri,$params,$message,$ocupacion,$nombre)
    {
        $log = new Logger();
        $log->method = $method;
        $log->uri = $uri;
        $log->params = $params;
        $log->message = $message;
        $log->ocupacion = $ocupacion;
        $log->nombre = $nombre;
        $log->crearLogger();
    }

    public static function TraerPorSector($request, $response, $args)
    {
        $parametros = $request->getParsedBody();   
        $mensaje = Logger::TraerPorSector($parametros['sector']);
        $payload = json_encode(array("Logs" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function TraerPorGrupoSector($request, $response, $args)
    { 
        $mensaje = Logger::TraerPorGrupoSector();
        $payload = json_encode(array("Logs" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function TraerPorEmpleado($request, $response, $args)
    { 
        $parametros = $request->getParsedBody();   
        $mensaje = Logger::TraerPorEmpleado($parametros['empleado']);
        $payload = json_encode(array("Logs" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}