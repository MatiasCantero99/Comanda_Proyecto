<?php
require_once './models/Pedido.php';
require_once './models/ConceptoPedido.php';
require_once './utils/AutentificadorJWT.php';

class PedidoController
{
    public static function ValidarFoto($foto,$datos, $parametros){
        $response = "";
        if ($foto['foto']->getError() === !UPLOAD_ERR_OK || $foto['foto']->getSize() === 0){
            $response .= "No hay foto. ";
        }
        if (!Validador::ValidarTipoEspecifico($datos->ocupacion, 'mozo')){
          $response .= "El usuario no es mozo. ";
        }
        if (!Validador::ValidarInt($parametros['mesa'])){
            $response .= "La mesa no es numerica. ";
          }
        return $response;
    }
    public static function ValidarPedido($usuario,$datos){
        $response = "";
        if (!Validador::ValidarSTR($usuario['pedido'])){
            $response .= "El pedido no es texto. ";
        }
        if (!Validador::ValidarSTR($usuario['mozo'])){
            $response .= "el precio no es correcta. ";
        }
        if (!Validador::ValidarTipoEspecifico($datos->ocupacion, 'mozo')){
          $response .= "El usuario no es mozo. ";
        }
        if (!Validador::ValidarInt($usuario['mesa'])){
            $response .= "La mesa no es numerico ";
        }
        return $response;
    }

    public static function ValidarLista($datos){
        $response = "";
        if (!Validador::ValidarTipo($datos->ocupacion)){
          $response .= "El tipo no es bartender, mozo, cocinero o cervecero . ";
        }
        return $response;
    }
    public function CargarUno($request, $response, $args)
    {
        $numeroPedido = $this->generarCodigoAlfanumerico();
        $numeroPedidoIndividual = $this->generarCodigoAlfanumerico();
        $parametros = $request->getParsedBody();
        $conceptoPedido = new ConceptoPedido();
        $conceptoPedido->estado = 'pendiente';
        $conceptoPedido->mesa = $parametros["mesa"];
        $conceptoPedido->numeroPedidoIndividual = $numeroPedidoIndividual;
        $conceptoPedido->numeroPedido = $numeroPedido;
        $conceptoPedido->nombre = str_replace(' ', '_', $parametros["pedido"]);
        
        $pedido = new Pedido();
        $pedido->mozoAsignado = $parametros["mozo"];
        $pedido->mesa = $parametros["mesa"];
        $pedido->estado = "pendiente";
        $pedido->numeroPedido = $numeroPedido;
        $resultado = $pedido->verificarPedido();
        if($resultado !== ''){
            $conceptoPedido->numeroPedido = $resultado;
            $mensaje = $conceptoPedido->crearConceptoPedido();
        }
        else{
            $mensaje = $conceptoPedido->crearConceptoPedido();
            echo "asae";
            if($mensaje !== 'Nombre del Producto no encontrado'){
                echo "asa";
                $pedido->crearPedido();
            }

        }
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarFoto($request, $response, $args)
    { 
        $parametros = $request->getParsedBody();
        $numeroPedido = Pedido::obtenerNumeroPedido($parametros['mesa']);
        $foto = $request->getUploadedFiles();
        $mensaje = Pedido::guardarFotoEnCarpeta($foto['foto'],$numeroPedido,$parametros['mesa']);
        $payload = json_encode(array("mensaje" => $mensaje));

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

    public function TraerLista($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = Pedido::obtenerListaPorTipo($datos->ocupacion);
        $payload = json_encode($mensaje);

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
}
