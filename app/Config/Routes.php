<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('Bridging_apotek/(:any)', 'Bridging_apotek::$1');
$routes->get('Bridging_apotek/(:any)', 'Bridging_apotek::$1');

$routes->get('tes', 'Bridging_apotek::index');

$routes->group('/', ['namespace' => 'App\Controllers'], static function ($routes) {
    // ... route default CI4

    // Route untuk API BPJS
    $routes->group('bpjs', static function ($routes) {
        $routes->get('peserta/nokartu/(:num)', 'BpjsController::getPesertaByNoKartu/$1');
        $routes->get('peserta/nik/(:num)', 'BpjsController::getPesertaByNik/$1');
        $routes->post('sep', 'BpjsController::createSEP');
    });

    // Route untuk monitoring
    $routes->get('monitoring', 'MonitoringController::index');
});