<?php
require_once './utils/Autentificador.php';
require_once './utils/validador.php';

class IngresoController
{
    public static function ValidarLogin($pdt){
        $response = "";
        if (!Validador::ValidarInt($pdt['id'])){
            $response .= "El id no es numerico. ";
        }
        if (!Validador::ValidarSTR($pdt['clave'])){
            $response .= "La clave no es texto. ";
        }
        return $response;
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
