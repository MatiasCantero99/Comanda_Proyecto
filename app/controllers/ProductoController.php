<?php
require_once './models/Productos.php';

class ProductoController
{
  public static function ValidarProducto($producto,$datos){
    $response = "";
    if (!Validador::ValidarSTR($producto['nombre'])){
        $response .= "El nombre no es texto. ";
    }
    if (!Validador::ValidarInt($producto['precio'])){
        $response .= "el precio no es correcta. ";
    }
    if (!Validador::ValidarTipoEspecifico($datos->ocupacion, 'socio')){
      $response .= "El usuario no es socio. ";
    }
    if (!Validador::ValidarInt($producto['stock'])){
        $response .= "El stock no es numerico ";
    }
    if (!Validador::ValidarTipoProducto($producto['encargado'])){
      $response .= "El encargado no es bartender, cocinero, cervecero.";
  }
    return $response;
}
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $fechaVencimiento = new DateTime();
        $fechaVencimiento->modify('+45 days');
        $pds = new Productos();
        $pds->nombre = str_replace(' ', '_', $parametros["nombre"]);
        $pds->stock = $parametros["stock"];
        $pds->encargado = $parametros["encargado"];
        $pds->precio = $parametros["precio"];
        $pds->fechaIngreso = (new DateTime())->format('Y-m-d');
        $pds->fechaVencimiento = $fechaVencimiento->format('Y-m-d');
        $pds->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por nombre
        $pds = $args['nombre'];
        $producto = Productos::obtenerProducto($pds);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Productos::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

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
