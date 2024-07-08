<?php
require_once './models/Mesa.php';

class MesaController
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        // Creamos el producto
        $codigo = $this->generarCodigoAlfanumerico();
        $mesa = new Mesa();
        $mesa->numero = $parametros["numero"];
        $mesa->estado = "vacio";
        $mesa->fechaIngreso = (new DateTime())->format('Y-m-d');
        $mesa->codigo = $codigo;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function generarCodigoAlfanumerico($longitud = 5) 
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
        $longitudCaracteres = strlen($caracteres);
    
        $codigo = '';
    
        for ($i = 0; $i < $longitud; $i++) 
        {
            $caracterAleatorio = $caracteres[rand(0, $longitudCaracteres - 1)];
    
            $codigo .= $caracterAleatorio;
        }
    
        return $codigo;
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por numero
        $mesaNum = $args['numero'];
        $mesa = Mesa::obtenerMesa($mesaNum);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function cambiarCobrar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        Mesa::modificarCobrar($parametros['codigo']);
        $mensaje = 'Mesa cambiada con exito';
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function cambiarCerrar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        Mesa::modificarCerrar($parametros['codigo']);
        $mensaje = 'Mesa cambiada con exito';
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
