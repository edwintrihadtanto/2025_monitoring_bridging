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
        
        $routes->group('referensi', static function ($routes) {
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/obat/1/2024-09-01/asam
            $routes->get('getobat/(:segment)/(:segment)/(:segment)', 'BpjsController::getReferensiObat/$1/$2/$3');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/spesialistik/
            $routes->get('getspesialistik', 'BpjsController::getReferensiSpesialistik');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/settingppk/0182A007
            $routes->get('getsettingppk/(:segment)', 'BpjsController::getReferensiSettingPpk/$1');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/ppk/1/pus
            $routes->get('getppk/(:segment)/(:segment)', 'BpjsController::getReferensiPpk/$1/$2');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/poli/da
            $routes->get('getpoli/(:segment)', 'BpjsController::getReferensiPoli/$1');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/dpho
            $routes->get('getdpho', 'BpjsController::getReferensiDpho');
        });

        $routes->group('insert', static function ($routes) {
            $routes->post('obatnonracikan', 'BpjsInsertController::obatnonracikan');
            $routes->post('obatracikan', 'BpjsInsertController::obatracikan');
            $routes->post('updatestokobat', 'BpjsInsertController::updatestokobat');

            $routes->post('sjpresep', 'BpjsInsertController::sjpresep');
            $routes->get('daftarresep/(:segment)/(:segment)', 'BpjsController::daftarresep/$1/$2');
        });

        $routes->group('delete', static function ($routes) {
            $routes->delete('del_pelayananobat', 'BpjsDeleteController::del_pelayananobat');
            $routes->delete('del_hapusresep', 'BpjsDeleteController::del_hapusresep');
        });

        // http://localhost/2025_monitoring_bridging/bpjs/listpelayananobat_perSEP/1801R0010419V000001
        $routes->get('listpelayananobat_perSEP/(:segment)', 'BpjsController::getListPelayananObat/$1');
        // http://localhost/2025_monitoring_bridging/bpjs/riwayatpelayananobat/2024-09-01/2024-09-01/12345678
        $routes->get('riwayatpelayananobat/(:segment)/(:segment)/(:segment)', 'BpjsController::getRiwayatPelayananObat/$1/$2/$3');

        $routes->get('monitoringklaim/(:num)/(:num)/(:num)/(:num)', 'BpjsController::getMonitoringKlaim/$1/$2/$3/$4');
        $routes->get('rekapprb/(:num)/(:num)', 'BpjsController::getRekapPasienPRB/$1/$2');

    });

    // Sidebar
    $routes->get('monitoring', 'MonitoringController::index', ['filter' => 'auth']);

    $routes->get('sidebar-faskes', 'ReferensiController::viewfaskes', ['filter' => 'auth']);
    $routes->post('ref/search_faskes', 'ReferensiController::search_faskes', ['filter' => 'auth']);
    $routes->get('sidebar-apotik', 'ReferensiController::viewapotik', ['filter' => 'auth']);
    $routes->post('ref/search_apotik', 'ReferensiController::search_apotik', ['filter' => 'auth']);
    $routes->get('sidebar-poli', 'ReferensiController::viewpoli', ['filter' => 'auth']);
    $routes->post('ref/search_poli', 'ReferensiController::search_poli', ['filter' => 'auth']);
    $routes->get('sidebar-dpho', 'ReferensiController::viewdpho', ['filter' => 'auth']);
    $routes->post('ref/search_dpho', 'ReferensiController::search_dpho', ['filter' => 'auth']);
    $routes->get('sidebar-obat', 'ReferensiController::viewobat', ['filter' => 'auth']);
    $routes->post('ref/search_obat', 'ReferensiController::search_obat', ['filter' => 'auth']);    
    $routes->get('sidebar-spesialis', 'ReferensiController::viewspesialis', ['filter' => 'auth']);
    $routes->post('ref/search_spesialis', 'ReferensiController::search_spesialis', ['filter' => 'auth']);

    $routes->get('sidebar-daftarresep', 'BpjsInsertController::viewgetdaftar_resep', ['filter' => 'auth']);
    $routes->post('res/search_listResep', 'BpjsInsertController::getdaftarresep', ['filter' => 'auth']);
    
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