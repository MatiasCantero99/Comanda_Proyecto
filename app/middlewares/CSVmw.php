<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CSVmw
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $csv = $request->getUploadedFiles();

        // Verificar que el archivo existe
        if (!isset($csv['datos_productos']) || $csv['datos_productos']->getError() !== UPLOAD_ERR_OK) {
            $response = new Response();
            $response->getBody()->write(json_encode(["error" => "Archivo CSV no encontrado o error al cargar."]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $archivo = $csv['datos_productos']->getStream()->getMetadata('uri');
        $keys = ["nombre", "encargado", "precio", "stock"];
        $mensaje = [];
        $error = false;

        if (($handle = fopen($archivo, 'r')) !== FALSE) {
            $cabecera = fgetcsv($handle, 10000, ',');
            // Validar las claves del CSV
            if ($cabecera !== $keys) {
                $response = new Response();
                $response->getBody()->write(json_encode(["error" => "Cabecera del CSV inválida. Se esperaban: " . implode(', ', $keys)]));
                fclose($handle);
                return $response->withHeader('Content-Type', 'application/json');
            }

            // Validar valores
            while (($data = fgetcsv($handle, 10000, ',')) !== FALSE) {
                $fila = array_combine($cabecera, $data);
                
                // Validación de 'nombre'
                if (!is_string($fila["nombre"]) || empty($fila["nombre"])) {
                    $mensaje[] = ["error" => "El nombre debe ser un string no vacío."];
                    $error = true;
                }

                // Validación de 'encargado'
                $array_encargado = ['cocinero', 'bartender', 'cervecero'];
                if (!in_array($fila["encargado"], $array_encargado)) {
                    $mensaje[] = ["error" => "El encargado de producto debe ser cocinero, bartender, cervecero."];
                    $error = true;
                }

                // Validación de 'precio'
                if (!is_numeric($fila["precio"]) || $fila["precio"] <= 0) {
                    $mensaje[] = ["error" => "El precio debe ser un valor numérico positivo."];
                    $error = true;
                }

                // Validación de 'stock'
                if (!is_numeric($fila["stock"]) || $fila["stock"] <= 0) {
                    $mensaje[] = ["error" => "El stock debe ser un valor numérico positivo."];
                    $error = true;
                }

                if ($error) break; // Salir si hay error en cualquier fila
            }
            fclose($handle);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(["error" => "No se puede abrir el archivo."]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        if ($error) {
            $response = new Response();
            $payload = json_encode($mensaje);
            $response->getBody()->write($payload);
        } else {
            $response = $handler->handle($request);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}