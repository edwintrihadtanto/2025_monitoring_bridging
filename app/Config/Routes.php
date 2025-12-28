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
 
    // Route untuk API BPJS
    $routes->group('bpjs', static function ($routes) {
        // API VCLAIM
        $routes->get('peserta/nokartu/(:num)', 'BpjsController::getPesertaByNoKartu/$1');
        $routes->get('peserta/nik/(:num)', 'BpjsController::getPesertaByNik/$1');
        // END API VCLAIM
        $routes->post('sep', 'BpjsController::createSEP');

        // --- ROUTE UNTUK REFERENSI DI SINI ---
        $routes->group('referensi', static function ($routes) {
            $routes->get('obat/(:segment)/(:segment)/(:segment)', 'BpjsController::getReferensiObat/$1/$2/$3');
            $routes->get('spesialistik', 'BpjsController::getReferensiSpesialistik');
            $routes->get('settingppk/(:segment)', 'BpjsController::getReferensiSettingPpk/$1');
            $routes->get('ppk/(:segment)/(:segment)', 'BpjsController::getReferensiPpk/$1/$2');
            $routes->get('poli/(:segment)', 'BpjsController::getReferensiPoli/$1');
            $routes->get('dpho', 'BpjsController::getReferensiDpho');
        });

    });

    // --- TAMBAHKAN FILTER DI SINI (Bagian Monitoring di dalam Group) ---
    $routes->get('monitoring', 'MonitoringController::index', ['filter' => 'auth']);

    $routes->get('/profile', 'Profile::index');
    $routes->post('/profile/update', 'Profile::updatePassword');

    // TAMBAHKAN ROUTE PENCARIAN PASIEN UI
    $routes->get('pasien', 'BpjsPasienController::index');
    $routes->post('pasien/search', 'BpjsPasienController::search');
});

 $routes->get('/BridgingTES/(:any)', 'BridgingTES::$1');