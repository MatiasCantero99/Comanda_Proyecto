<?php
require_once './models/Pedido.php';
require_once './models/Mesa.php';
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
        if (!Validador::ValidarSTR($usuario['nombreCliente'])){
            $response .= "El nombre no es texto. ";
        }
        if (!Validador::ValidarTipoEspecifico($datos->ocupacion, 'mozo')){
          $response .= "El usuario no es mozo. ";
        }
        if (!Validador::ValidarInt($usuario['mesa'])){
            $response .= "La mesa no es numerico ";
        }
        return $response;
    }

    public static function ValidarCambio($usuario,$datos){
        $response = "";
        if (!Validador::ValidarSTR($usuario['codigo'])){
            $response .= "El pedido no es alfanumerico. ";
        }
        if (!Validador::ValidarInt($usuario['tiempo'])){
            $response .= "El tiempo no es numerico. ";
        }
        if (!Validador::ValidarTipo($datos->ocupacion)){
          $response .= "El usuario es incorrecto. ";
        }
        return $response;
    }

    public static function ValidarListo($usuario,$datos){
        $response = "";
        if (!Validador::ValidarSTR($usuario['codigo'])){
            $response .= "El pedido no es alfanumerico. ";
        }
        if (!Validador::ValidarTipo($datos->ocupacion)){
          $response .= "El usuario es incorrecto. ";
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
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        
        $numeroPedido = $this->generarCodigoAlfanumerico();
        $numeroPedidoIndividual = $this->generarCodigoAlfanumerico();
        $parametros = $request->getParsedBody();
        $conceptoPedido = new ConceptoPedido();
        $conceptoPedido->estado = 'pendiente';
        $conceptoPedido->mesa = $parametros["mesa"];
        $conceptoPedido->numeroPedidoIndividual = $numeroPedidoIndividual;
        $conceptoPedido->numeroPedido = $numeroPedido;
        $conceptoPedido->nombre = str_replace(' ', '_', $parametros["pedido"]);
        
        Usuario::asignarMesa($datos->id,$parametros['mesa']);
        
        $pedido = new Pedido();
        $pedido->mozoAsignado = $datos->id;
        $pedido->mesa = $parametros["mesa"];
        $pedido->estado = "pendiente";
        $pedido->numeroPedido = $numeroPedido;
        $pedido->nombre = $parametros["nombreCliente"];
        $resultado = $pedido->verificarPedido();
        if($resultado !== ''){
            $conceptoPedido->numeroPedido = $resultado;
            $mensaje = $conceptoPedido->crearConceptoPedido();
            $mensaje .= ' numero de pedido:' . $resultado;
        }
        else{
            $mensaje = $conceptoPedido->crearConceptoPedido();
            $mensaje .= ' numero de pedido:' . $numeroPedido;
            if($mensaje !== 'Nombre del Producto no encontrado'){
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

    public function CambiarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        ConceptoPedido::cambiarPedido($parametros['codigo'],$parametros['tiempo'],$datos->id);
        ConceptoPedido::cambiarTiempo($parametros['codigo'],$parametros['tiempo']);

        $mensaje = 'pedido actualizado';
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CambiarPedidoListo($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        ConceptoPedido::cambiarPedidoAListo($parametros['codigo']);

        $mensaje = 'pedido actualizado';
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
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

    public function TraerEnPreparacion($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = Pedido::obtenerListaPreparacion($datos->id);
        $payload = json_encode($mensaje);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorCodigos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();   
        $mensaje = Pedido::obtenerListaPorCodigos($parametros['codigoMesa'],$parametros['codigoPedido']);
        $payload = json_encode(array("pedidos" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraeParaSocio($request, $response, $args)
    {
        $mensaje = Pedido::TraeParaSocio();
        $payload = json_encode(array("pedidos" => $mensaje));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraeParaMozoListo($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $mensaje = Pedido::TraeParaMozoListo($datos->id);
        foreach($mensaje as $fila){
            Mesa::modificarEstado($fila['mesa']);
        }
        
        $payload = json_encode(array("pedidos" => $mensaje));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function cobrar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $datos = AutentificadorJWT::ObtenerData($token);
        $precio = Pedido::TraePrecioACobrar($parametros['codigo']);
        $mensaje = Pedido::AgregarCobro($precio,$parametros['codigo']);
        
        $payload = json_encode(array("pedidos" => $mensaje));

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
        $precio = Pedido::TraePrecioACobrar($parametros['codigo']);
        $mensaje = Pedido::AgregarCobro($precio,$parametros['codigo']);
        
        $payload = json_encode(array("pedidos" => $mensaje));

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
}
