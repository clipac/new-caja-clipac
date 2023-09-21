<?php
/** @var \Laravel\Lumen\Routing\Router $router */
/*
 *
|--------------------------------------------------------------------------| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    echo gethostname().'<br>';
    return $router->app->version();
});
$router->get('/ticket/{tipo}/{doc}', 'TicketController@imprimir');
$router->get('/documento/{id}', 'TicketController@print');
$router->get('/acto-medico/{nroacto}/{usuario}', 'ActoMedicoController@print');
$router->get('/print/{idventa}/{idcaja}', 'TicketController@sendPrint');
$router->get('/printNote/{nro_secuencia}/{id_puntoventa}/{idcaja}', 'PedidoNoteController@printNote');
$router->get('/printNotaPedido/{nro_pedido}/{id_puntoventa}', 'PedidoNoteController@printNotaPedido');
$router->get('/hostname','HostNameController@getinfo');
$router->get('/hostname/{ocupacional}','HostNameController@getinfo');
$router->get('/documentointernosalida/print/{id}', 'Farmacia\DocumentoInternoSalidaController@print');
$router->get('/ticket-cierre/{codigo}','CajaController@imprimirCierre');
$router->post('/asignar-caja','HostNameController@asignarCaja');
