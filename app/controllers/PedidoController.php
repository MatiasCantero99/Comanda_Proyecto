<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->comida = $parametros["comida"];
        $pedido->cantidadComida = $parametros["cantidadComida"];
        $pedido->bebida = $parametros["bebida"];
        $pedido->cantidadBebida = $parametros["cantidadBebida"];
        $pedido->mozoAsignado = $parametros["mozo"];
        $pedido->estado = "pendiente";
        $pedido->numeroPedido = $this->generarCodigoAlfanumerico();
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

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
        $numPedido = $args['numero'];
        $mesa = Pedido::obtenerPedido($numPedido);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

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
