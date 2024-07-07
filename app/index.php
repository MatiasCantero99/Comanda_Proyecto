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

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/IngresoController.php';

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
    // $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');

    $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add([new Usuariosmw(),'usuarioValidados'])
    ->add([new Usuariosmw(),'usuarioSeteados'])
    ->add([new Ingresomw(),'verificarToken']);

    $group->get('/PDF', \UsuarioController::class . ':PDF');
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
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{numero}', \MesaController::class . ':TraerUno');
    $group->post('[/]', \MesaController::class . ':CargarUno');
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

    $group->post('/cambiarPedido', \PedidoController::class . ':CambiarPedido')
    ->add([new Pedidomw(),'cambiarValidados'])
    ->add([new Pedidomw(),'cambiarSeteados'])
    ->add([new Ingresomw(),'verificarToken']);
  });

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
