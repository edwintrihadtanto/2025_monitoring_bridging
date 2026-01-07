<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 $routes->get('/', 'Auth::login');
 $routes->get('/login', 'Auth::login');
 $routes->post('/auth/attemptLogin', 'Auth::attemptLogin');
 $routes->get('/logout', 'Auth::logout');

 $routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);


 // Agar API BPJS juga tidak bisa diakses sembarangan
 $routes->post('Bridging_apotek/(:any)', 'Bridging_apotek::$1', ['filter' => 'auth']);
 $routes->get('Bridging_apotek/(:any)', 'Bridging_apotek::$1', ['filter' => 'auth']);


 $routes->group('/', ['namespace' => 'App\Controllers'], static function ($routes) {
 
    // $routes->group('bpjs', ['namespace' => 'App\Controllers', 'filter' => 'internal'], static function ($routes) {
    $routes->group('bpjs', static function ($routes) {
        // API VCLAIM
        $routes->get('peserta/nokartu/(:num)', 'BpjsController::getPesertaByNoKartu/$1');
        $routes->get('peserta/nik/(:num)', 'BpjsController::getPesertaByNik/$1');
        // END API VCLAIM
        $routes->get('getSEPPasien/(:segment)', 'BpjsController::searchingSEPPasien/$1');
        // $routes->post('sep', 'BpjsController::createSEP');
        
        // --- ROUTE UNTUK REFERENSI DI SINI ---
        $routes->group('referensi', static function ($routes) {
            $routes->get('obat/(:segment)/(:segment)/(:segment)', 'BpjsController::getReferensiObat/$1/$2/$3');
            $routes->get('spesialistik', 'BpjsController::getReferensiSpesialistik');
            $routes->get('settingppk/(:segment)', 'BpjsController::getReferensiSettingPpk/$1');
            $routes->get('ppk/(:segment)/(:segment)', 'BpjsController::getReferensiPpk/$1/$2');
            $routes->get('poli/(:segment)', 'BpjsController::getReferensiPoli/$1');
            $routes->get('dpho', 'BpjsController::getReferensiDpho');
        });

        $routes->get('monitoringklaim/(:num)/(:num)/(:num)/(:num)', 'BpjsController::getMonitoringKlaim/$1/$2/$3/$4');
        $routes->get('rekapprb/(:num)/(:num)', 'BpjsController::getRekapPasienPRB/$1/$2');

    });

    // Sidebar
    $routes->get('monitoring', 'MonitoringController::index', ['filter' => 'auth']);

    $routes->get('/profile', 'Profile::index', ['filter' => 'auth']);
    $routes->post('/profile/update', 'Profile::updatePassword');

    $routes->get('/pasien', 'BpjsPasienController::index', ['filter' => 'auth']);
    $routes->post('pasien/search', 'BpjsPasienController::search', ['filter' => 'auth']);

    $routes->get('/seppasien', 'BpjsPasienController::fomrseppasien', ['filter' => 'auth']);
    $routes->post('pasien/searchsep', 'BpjsPasienController::searchsep', ['filter' => 'auth']);

    $routes->get('/monitoringklaim', 'BpjsPasienController::viewmonitoring', ['filter' => 'auth']);
    $routes->post('pasien/monitoring_obat', 'BpjsPasienController::getmonitoring_obat', ['filter' => 'auth']);

});

 $routes->get('/BridgingTES/(:any)', 'BridgingTES::$1');