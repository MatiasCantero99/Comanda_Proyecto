<?php
require_once './models/Usuario.php';
require_once './utils/Autentificador.php';
require_once './utils/validador.php';
require_once './utils/PDF.php';

class UsuarioController
{
  public static function ValidarUsuario($usuario,$datos){
    $response = "";
    if (!Validador::ValidarSTR($usuario['usuario'])){
        $response .= "El usuario no es texto. ";
    }
    if (!Validador::ValidarSTR($usuario['clave'])){
        $response .= "La clave no es correcta. ";
    }
    if (!Validador::ValidarTipo($usuario['ocupacion'])){
        $response .= "El tipo no es bartender, mozo, cocinero, cervecero o socio. ";
    }
    if (!Validador::ValidarTipoEspecifico($datos->ocupacion, 'socio')){
      $response .= "El usuario no es socio. ";
  }
    if (!Validador::ValidarInt($usuario['edad'])){
        $response .= "La edad no es numerica. ";
    }
    if (!Validador::ValidarSTR($usuario['nombre'])){
      $response .= "El nombre no es texto. ";
  }
    return $response;
}

public static function ValidarCodigos($codigo){
  $response = "";
  if (!Validador::ValidarSTR($codigo['codigoMesa'])){
      $response .= "El codigoMesa no es texto. ";
  }
  if (!Validador::ValidarSTR($codigo['codigoPedido'])){
      $response .= "El codigoPedido no es correcta. ";
  }
  return $response;
}
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      
      // Creamos el usuario
      $usr = new Usuario();
      $usr->usuario = $usuario;
      $usr->clave = $clave;
      $usr->fechaAlta = (new DateTime())->format('Y-m-d');
      $usr->ocupacion = $parametros["ocupacion"];
      $usr->edad = $parametros["edad"];
      $usr->nombre = $parametros["nombre"];
      $usr->crearUsuario();
      
      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function PDF($request, $response, $args)
    {
        $lista = Usuario::PDF();
        $payload = json_encode(array("listaUsuario" => $lista));
        $pdf = new PDFGenerator();
        $pdf->generatePDFFromAssocArray($lista);
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

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

    public function Ingresar($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      //$nombre = $parametros['nombre'];
      $clave = $parametros['clave'];
      $id = $parametros['id'];

      $datos = array('clave' => $clave, 'id' => $id);

      $usuario = Autentificador::Ingresar($datos);
      if (empty($usuario)){
        $payload = "id incorrecto";
      }
      else{
        if(password_verify($clave, $usuario->clave)){
          $datosAGuardar = array('nombre' => $usuario->usuario, 'ocupacion' => $usuario->ocupacion,'mesaOcupada' => $usuario->mesaOcupada,'id' => $usuario->id);
          
          $token = AutentificadorJWT::CrearToken($datosAGuardar);
          $payload = json_encode(array('jwt' => $token));

        }
        else{
          $payload = "Clave incorrecta";
        }
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
