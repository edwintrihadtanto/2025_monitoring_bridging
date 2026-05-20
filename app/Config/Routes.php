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

 // $routes->post('Bridging_apotek/(:any)', 'Bridging_apotek::$1', ['filter' => 'auth']);
 // $routes->get('Bridging_apotek/(:any)', 'Bridging_apotek::$1', ['filter' => 'auth']);


 $routes->group('/', ['namespace' => 'App\Controllers'], static function ($routes) {
 
    // $routes->group('bpjs', ['namespace' => 'App\Controllers', 'filter' => 'internal'], static function ($routes) {
    $routes->group('bpjs', static function ($routes) {
        // API VCLAIM
        $routes->get('peserta/nokartu/(:num)', 'BpjsController::getPesertaByNoKartu/$1');
        $routes->get('peserta/nik/(:num)', 'BpjsController::getPesertaByNik/$1');
        $routes->get('createSEPBPJS_IGD', 'BpjsController::createSEPBPJS_IGD');
        $routes->get('createSEPBPJS_JALAN', 'BpjsController::createSEPBPJS_JALAN');
        $routes->get('createSEPBPJS_JALAN2', 'BpjsController::createSEPBPJS_JALAN2');
        $routes->get('createSEPBPJS_INAP', 'BpjsController::createSEPBPJS_INAP');
        $routes->get('deleteSEPBPJS', 'BpjsController::delSEP');
        $routes->get('createRUJUKAN', 'BpjsController::createRUJUKAN');
        $routes->get('batalRUJUKAN', 'BpjsController::batalRUJUKAN');
        $routes->get('buatSPRI', 'BpjsController::buatSPRI');
        $routes->get('approval', 'BpjsController::approval');
        $routes->get('getfinger', 'BpjsController::getfinger');
        $routes->get('createPostMRS', 'BpjsController::createPostMRS');
        $routes->get('batalPostMRS', 'BpjsController::batalPostMRS');
        $routes->get('createBackdate', 'BpjsController::createBackdate');
        $routes->get('getRencanaKontrol', 'BpjsController::getRencanaKontrol');
        $routes->get('getRujukan/(:segment)', 'BpjsController::getRujukan/$1');
        // END API VCLAIM 

        $routes->get('getSEPPasien/(:segment)', 'BpjsController::searchingSEPPasien/$1');
        
        $routes->group('referensi', static function ($routes) {
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/obat/1/2024-09-01/asam
            $routes->get('getobat/(:segment)/(:segment)/(:segment)', 'BpjsController::getReferensiObat/$1/$2/$3');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/spesialistik/
            $routes->get('getspesialistik', 'BpjsController::getReferensiSpesialistik');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/settingppk/0182A007
            $routes->get('getsettingppk/(:segment)', 'BpjsController::getReferensiSettingPpk/$1');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/ppk/1/pus
            $routes->get('getppk/(:segment)/(:segment)/(:segment)', 'BpjsController::getReferensiPpk/$1/$2/$3');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/poli/da
            $routes->get('getpoli/(:segment)', 'BpjsController::getReferensiPoli/$1');
            // http://localhost/2025_monitoring_bridging/bpjs/referensi/dpho
            $routes->get('getdpho', 'BpjsController::getReferensiDpho');
        });

        $routes->group('insert', static function ($routes) {

            $routes->post('obatnonracikan', 'BpjsController::obatnonracikan');
            $routes->get('obatnonracikan', 'BpjsController::obatnonracikan');
            $routes->post('obatracikan', 'BpjsController::obatracikan');
            $routes->get('obatracikan', 'BpjsController::obatracikan');
            $routes->get('updatestokobat', 'BpjsController::updatestokobat'); //masih gagal Unauthorized!

            // $routes->get('getsjpresep/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'BpjsController::sjpresep/$1/$2/$3/$4/$5/$6/$7/$8');
            $routes->get('sjpresep/(:segment)', 'BpjsController::getkirimresep/$1'); //buat tes langsung insert header resep
            $routes->post('insresepobat', 'BpjsInsertController::kirimresep', ['filter' => 'auth']);
            $routes->get('getkirimresep/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'BpjsController::getkirimresep/$1/$2/$3/$4/$5/$6/$7/$8/$9');
            // $routes->get('obatnonracikan/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'BpjsController::getkirimresep/$1/$2/$3/$4/$5/$6/$7/$8/$9');
            
            $routes->get('daftarresep/(:segment)/(:segment)/(:segment)/(:segment)', 'BpjsController::daftarresep/$1/$2/$3/$4');
        });

        $routes->group('delete', static function ($routes) {
            $routes->get('del_hapusresep/(:segment)/(:segment)/(:segment)/(:segment)', 'BpjsController::del_hapusresep/$1/$2/$3/$4');
            $routes->get('del_hapusobat/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'BpjsController::hapusobat/$1/$2/$3/$4/$5');
            $routes->get('hapusobatX', 'BpjsController::hapusobatX');
        });

        // http://localhost/2025_monitoring_bridging/bpjs/listpelayananobat_perSEP/1801R0010419V000001
        $routes->get('listpelayananobat_perSEP/(:segment)/(:segment)', 'BpjsController::getListPelayananObat/$1/$2');
        $routes->get('riwayatpelayananobat/(:segment)/(:segment)/(:segment)/(:segment)', 'BpjsController::getRiwayatPelayananObat/$1/$2/$3/$4');

        $routes->get('monitoringklaim/(:num)/(:num)/(:num)/(:num)', 'BpjsController::getMonitoringKlaim/$1/$2/$3/$4');
        $routes->get('rekapprb/(:num)/(:num)', 'BpjsController::getRekapPasienPRB/$1/$2');

    });

    // Sidebar
    $routes->get('monitoring', 'MonitoringController::index', ['filter' => 'auth']);
    $routes->get('koneksi', 'MonitoringController::koneksi', ['filter' => 'auth']);
    $routes->get('koneksi/status', 'MonitoringController::koneksiStatus', ['filter' => 'auth']);

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
    $routes->get('getStatusResepBPJS', 'BpjsInsertController::getStatusResepBPJS', ['filter' => 'auth']);

    $routes->get('/profile', 'Profile::index', ['filter' => 'auth']);
    $routes->post('/profile/update', 'Profile::updatePassword');

    $routes->get('/pasien', 'BpjsPasienController::index', ['filter' => 'auth']);
    $routes->post('pasien/search', 'BpjsPasienController::search', ['filter' => 'auth']);

    $routes->get('/seppasien', 'BpjsPasienController::fomrseppasien', ['filter' => 'auth']);
    $routes->post('pasien/searchsep', 'BpjsPasienController::searchsep', ['filter' => 'auth']);

    $routes->get('/monitoringklaim', 'BpjsPasienController::viewmonitoring', ['filter' => 'auth']);
    $routes->post('pasien/monitoring_obat', 'BpjsPasienController::getmonitoring_obat', ['filter' => 'auth']);

    $routes->get('sidebar-pelobat-listpersep', 'BpjsInsertController::viewpelobat_listpersep', ['filter' => 'auth']);
    $routes->post('pel_obat/search_dftarresep', 'BpjsInsertController::getdaftar_pelayanan', ['filter' => 'auth']);
    $routes->get('sidebar-pelobat-riwayat', 'BpjsInsertController::viewpelobat_riwayat', ['filter' => 'auth']);
    $routes->post('pel_obat/search_riwayat', 'BpjsInsertController::getriwayat_pelayanan', ['filter' => 'auth']);

    $routes->get('sidebar-ResepSIMRS', 'BpjsInsertController::viewresepsimrs', ['filter' => 'auth']);
    $routes->post('res/search_resepSIMRS', 'BpjsInsertController::getResepSIMRS', ['filter' => 'auth']);
    $routes->post('res/getDetailObat', 'BpjsInsertController::getDetailObatSIMRS', ['filter' => 'auth']);

    $routes->post('res/del_hapusresep', 'BpjsInsertController::del_hapusresep', ['filter' => 'auth']);
    $routes->post('res/del_itemobat', 'BpjsInsertController::del_itemobat', ['filter' => 'auth']);

 });

 $routes->get('/BridgingTES/(:any)', 'BridgingTES::$1');
