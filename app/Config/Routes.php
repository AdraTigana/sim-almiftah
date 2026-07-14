<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::login');

// Auth
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// Admin
$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('kurikulum', 'Admin\Kurikulum::index');
    $routes->post('kurikulum/mapel/create', 'Admin\Kurikulum::createMapel');
    $routes->post('kurikulum/mapel/delete/(:num)', 'Admin\Kurikulum::deleteMapel/$1');
    $routes->post('kurikulum/kategori/create', 'Admin\Kurikulum::createKategori');
    $routes->post('kurikulum/kategori/delete/(:num)', 'Admin\Kurikulum::deleteKategori/$1');
    $routes->get('kurikulum/kategori/get/(:num)', 'Admin\Kurikulum::getKategori/$1');
    $routes->post('kurikulum/kategori/update/(:num)', 'Admin\Kurikulum::updateKategori/$1');
    $routes->post('kurikulum/kriteria/create', 'Admin\Kurikulum::createKriteria');
    $routes->post('kurikulum/kriteria/delete/(:num)', 'Admin\Kurikulum::deleteKriteria/$1');
    $routes->get('kurikulum/kriteria/get/(:num)', 'Admin\Kurikulum::getKriteria/$1');
    $routes->post('kurikulum/kriteria/update/(:num)', 'Admin\Kurikulum::updateKriteria/$1');
    $routes->get('santri', 'Admin\Santri::index');
    $routes->post('santri/store', 'Admin\Santri::store');
    $routes->get('santri/get/(:num)', 'Admin\Santri::get/$1');
    $routes->post('santri/update/(:num)', 'Admin\Santri::update/$1');
    $routes->post('santri/delete/(:num)', 'Admin\Santri::delete/$1');
    $routes->post('santri/import-excel', 'Admin\Santri::importExcel');
    $routes->get('guru', 'Admin\Guru::index');
    $routes->get('guru/get/(:num)', 'Admin\Guru::get/$1');
    $routes->post('guru/store', 'Admin\Guru::store');
    $routes->post('guru/update/(:num)', 'Admin\Guru::update/$1');
    $routes->post('guru/delete/(:num)', 'Admin\Guru::delete/$1');
    $routes->post('guru/assign/(:num)', 'Admin\Guru::assign/$1');
    $routes->get('rombel', 'Admin\Rombel::index');
    $routes->post('rombel/create', 'Admin\Rombel::create');
    $routes->get('rombel/get/(:num)', 'Admin\Rombel::get/$1');
    $routes->post('rombel/update/(:num)', 'Admin\Rombel::update/$1');
    $routes->post('rombel/delete/(:num)', 'Admin\Rombel::delete/$1');
    $routes->get('rombel/get-walas/(:num)', 'Admin\Rombel::getWalas/$1');
    $routes->post('rombel/assign-walas/(:num)', 'Admin\Rombel::assignWalas/$1');
    $routes->get('tahun-ajar', 'Admin\TahunAjar::index');
    $routes->post('tahun-ajar/create', 'Admin\TahunAjar::create');
    $routes->post('tahun-ajar/edit/(:num)', 'Admin\TahunAjar::edit/$1');
    $routes->post('tahun-ajar/delete/(:num)', 'Admin\TahunAjar::delete/$1');
    $routes->post('tahun-ajar/set-active/(:num)', 'Admin\TahunAjar::setActive/$1');
    $routes->get('profil', 'Admin\Profil::index');
    $routes->post('profil/update', 'Admin\Profil::update');
    $routes->get('cetak', 'WaliKelas\Cetak::index');
    $routes->get('cetak/rombel/(:num)', 'WaliKelas\Cetak::rombel/$1');
    $routes->get('cetak/excel/(:num)/(:num)', 'WaliKelas\Cetak::excel/$1/$2');
});

// Guru/Ustadz (hanya role guru, bukan walas)
$routes->group('guru', ['filter' => 'role:guru'], static function ($routes) {
    $routes->get('/', 'Guru\Dashboard::index');
    $routes->get('dashboard', 'Guru\Dashboard::index');
    $routes->get('input-saya', 'Guru\InputSaya::index');
    $routes->post('input-saya/assign', 'Guru\InputSaya::selfAssign');
    $routes->get('nilai', 'Guru\Nilai::index');
    $routes->get('nilai/mapel/(:num)/kelas/(:num)', 'Guru\Nilai::mapel/$1/$2');
    $routes->get('nilai/siswa/(:num)/mapel/(:num)/kelas/(:num)', 'Guru\Nilai::siswa/$1/$2/$3');
    $routes->get('nilai/siswa/form/(:num)/mapel/(:num)/kelas/(:num)/kategori/(:num)', 'Guru\Nilai::formKategori/$1/$2/$3/$4');
    $routes->post('nilai/save', 'Guru\Nilai::save');
    $routes->post('nilai/save-akhir', 'Guru\Nilai::saveAkhir');
    $routes->get('nilai/get-siswa', 'Guru\Nilai::getSiswa');
    $routes->get('nilai/get-kategori', 'Guru\Nilai::getKategori');
    $routes->get('nilai/get-kriteria', 'Guru\Nilai::getKriteria');
    $routes->get('nilai/get-detail-kategori', 'Guru\Nilai::getDetailKategori');
    $routes->post('nilai/sync-batch', 'Guru\Nilai::syncBatch');
    $routes->post('presensi/save-batch', 'Guru\Presensi::saveBatch');
    $routes->post('presensi/sync-batch', 'Guru\Presensi::syncBatch');
    $routes->get('presensi/rekap', 'Guru\PresensiRekap::index');
    $routes->get('profil', 'Guru\Profil::index');
    $routes->post('profil/update', 'Guru\Profil::update');
});

// Wali Kelas
$routes->group('walas', ['filter' => 'role:walas'], static function ($routes) {
    $routes->get('/', 'WaliKelas\Dashboard::index');
    $routes->get('dashboard', 'WaliKelas\Dashboard::index');
    $routes->get('rapor', 'WaliKelas\Rapor::index');
    $routes->get('rapor/kelas/(:num)', 'WaliKelas\Rapor::kelas/$1');
    $routes->get('rapor/siswa/(:num)/(:num)', 'WaliKelas\Rapor::siswa/$1/$2');
    $routes->get('rekapitulasi', 'WaliKelas\Rekapitulasi::index');
    $routes->get('rekapitulasi/kelas/(:num)', 'WaliKelas\Rekapitulasi::kelas/$1');
    $routes->get('cetak', 'WaliKelas\Cetak::index');
    $routes->get('cetak/rombel/(:num)', 'WaliKelas\Cetak::rombel/$1');
    $routes->get('cetak/excel/(:num)/(:num)', 'WaliKelas\Cetak::excel/$1/$2');
    $routes->get('profil', 'WaliKelas\Profil::index');
    $routes->post('profil/update', 'WaliKelas\Profil::update');
});
