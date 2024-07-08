<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './utils/AutentificadorJWT.php';
// require_once './middlewares/Logger.php';
require_once './middlewares/logginmw.php';
require_once './middlewares/ingresomw.php';
require_once './middlewares/usuariosmw.php';
require_once './middlewares/productomw.php';
require_once './middlewares/pedidomw.php';
require_once './middlewares/fotomw.php';
require_once './middlewares/encuestamw.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/IngresoController.php';
require_once './controllers/EncuestaController.php';

// php -S localhost:666 -t app


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();


// Instantiate App
$app = AppFactory::create();

// Set base path
// $app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// JWT test
$app->group('/jwt', function (RouteCollectorProxy $group) {
  $group->post('/crearToken', \IngresoController::class . ':Ingresar')
  ->add([new Logginmw(),'loginValidados'])
  ->add([new Logginmw(),'loginsSeteados']);

  $group->get('/devolverPayLoad', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/devolverDatos', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });
});

//USUARIOS
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');

    $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add([new Usuariosmw(),'usuarioValidados'])
    ->add([new Usuariosmw(),'usuarioSeteados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->post('/mostrarPedido', \PedidoController::class . ':TraerPorCodigos')
    ->add([new Usuariosmw(),'codigosValidados'])
    ->add([new Usuariosmw(),'codigosSeteados']);

    $group->get('/PDF', \UsuarioController::class . ':PDF')
    ->add([new Pedidomw(),'validarSocio'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/descargar', \ProductoController::class . ':descargarCSV')
    ->add([new Pedidomw(),'validarSocio'])
    ->add([new Ingresomw(),'verificarToken']);

  });

//PRODUCTOS
  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{nombre}', \ProductoController::class . ':TraerUno');
    $group->post('[/]', \ProductoController::class . ':CargarUno')
    ->add([new Productomw(),'productoValidados'])
    ->add([new Productomw(),'productoSeteados'])
    ->add([new Ingresomw(),'verificarToken']);
  });

  //MESA
  $app->group('/mesa', function (RouteCollectorProxy $group) {
    $group->get('/ListaMesa', \MesaController::class . ':TraerTodos')
    ->add([new Pedidomw(),'validarSocio'])
    ->add([new Ingresomw(),'verificarToken']);
    $group->post('[/]', \MesaController::class . ':CargarUno');

    $group->post('/cambiarACobrar', \MesaController::class . ':cambiarCobrar')
    ->add([new Pedidomw(),'validarMozo'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->post('/cambiarACerrar', \MesaController::class . ':cambiarCerrar')
    ->add([new Pedidomw(),'validarSocio'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/masUsada', \MesaController::class . ':masUsada')
    ->add([new Pedidomw(),'validarSocio'])
    ->add([new Ingresomw(),'verificarToken']);
  });

  //ENCUESTA
  $app->group('/encuesta', function (RouteCollectorProxy $group) {
    $group->post('[/]', \EncuestaController::class . ':CargarUno')
    ->add([new Encuestamw(),'encuestaValidados'])
    ->add([new Encuestamw(),'encuestaSeteados']);

    $group->get('/traerEncuesta', \EncuestaController::class . ':TraerMejores')
    ->add([new Pedidomw(),'validarSocio'])
    ->add([new Ingresomw(),'verificarToken']);
  });


  //PEDIDO
  $app->group('/pedido', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->post('[/]', \PedidoController::class . ':CargarUno')
    ->add([new Pedidomw(),'pedidoValidados'])
    ->add([new Pedidomw(),'pedidoSeteados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->post('/cargarFoto', \PedidoController::class . ':CargarFoto')
    ->add([new Fotomw(),'fotoValidados'])
    ->add([new Fotomw(),'fotoSeteados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/listarPedidos', \PedidoController::class . ':TraerLista')
    ->add([new Pedidomw(),'listaValidados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/listarEnPreparacion', \PedidoController::class . ':TraerEnPreparacion')
    ->add([new Pedidomw(),'listaValidados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->post('/cambiarPedido', \PedidoController::class . ':CambiarPedido')
    ->add([new Pedidomw(),'cambiarValidados'])
    ->add([new Pedidomw(),'cambiarSeteados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->post('/cambiarAListo', \PedidoController::class . ':CambiarPedidoListo')
    ->add([new Pedidomw(),'listoValidados'])
    ->add([new Pedidomw(),'listoSeteados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/pedidoMozo', \PedidoController::class . ':TraeParaMozoListo')
    ->add([new Pedidomw(),'validarMozo'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/pedidoSocio', \PedidoController::class . ':TraeParaSocio')
    ->add([new Pedidomw(),'validarSocio'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/cobrar', \PedidoController::class . ':cobrar')
    ->add([new Pedidomw(),'validarMozo'])
    ->add([new Ingresomw(),'verificarToken']);
  });

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
