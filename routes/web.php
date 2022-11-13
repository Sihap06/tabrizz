<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', 'HomeController@index')->middleware('auth')->name('index');

Route::get('testing', function () {
    return view('admin.index');
});
Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::post('hapus_rekap', 'HomeController@hapus_rekap');
    Route::get('rekapStockToko/{id}', 'ShopController@rekapstocktoko');
    Route::get('rekapStock', 'ShopController@rekapstock');
    Route::post('userAktif', 'UserController@userAktif');
    Route::post('rekapExcel', 'HomeController@rekap_excel');
    Route::post('rekapTokoExcel', 'HomeController@rekap_toko_excel');
    Route::get('getstock/{id}', 'ShopController@getstock');
    Route::get('rekap', 'HomeController@rekap');
    Route::get('rekapPegawai/{id}', 'ShopController@rekapPegawai');
    Route::get('rekaptoko/{id}', 'ShopController@rekaptoko');
    Route::get('getrekaptoko/{id}', 'ShopController@getrekap');
    Route::get('getrekappegawai/{id}', 'ShopController@getrekappegawai');
    Route::get('getrekap', 'HomeController@getrekap');
    Route::post('post-product-toko', 'ShopController@addproduct');
    Route::post('post-user-toko', 'ShopController@adduser');
    Route::post('stock_toko', 'ShopController@addstock');
    Route::resource('product', 'ProductController');
    Route::resource('shop', 'ShopController');
    Route::resource('users', 'UserController');
    Route::resource('categories', 'CategoryController');
    Route::get('produk_toko/{id}', 'ShopController@product');
    Route::get('productcat/{id}', 'ShopController@getproductcat');
    Route::get('user_toko/{id}', 'ShopController@user');
    Route::post('edit_user_toko', 'ShopController@deletuser');
    Route::post('rekap_pdf', 'HomeController@rekap_pdf');
    Route::get('getproduk', [
        'uses' => 'ProductController@getproduk',
        'as' => 'ajax.get.produk',
    ]);
    Route::get('getproduktoko/{id}', 'ShopController@getproduct');
    Route::get('getusertoko/{id}', 'ShopController@getuser');

    Route::group(['prefix' => 'diskon'], function () {
        Route::get('', 'DiskonController@index')->name('diskon');
        Route::get('getdiskon', 'DiskonController@getDiskon')->name('ajax.get.produk.diskon');
    });

    Route::get('getshop', [
        'uses' => 'ShopController@getshop',
        'as' => 'ajax.get.shop',
    ]);
    Route::get('getuser', [
        'uses' => 'UserController@getuser',
        'as' => 'ajax.get.user',
    ]);
    Route::get('getcategory', [
        'uses' => 'CategoryController@getcategory',
        'as' => 'ajax.get.categories',
    ]);

    Route::get('ajax-chart-penjualan', 'HomeController@ajaxChartPenjualan');
    Route::get('ajax-chart-terjual', 'HomeController@ajaxChartTerjual');

    Route::get('ajax-chart-penjualan-shop/{id}', 'ShopController@ajaxChartPenjualan');
    Route::get('ajax-chart-terjual-shop/{id}', 'ShopController@ajaxChartTerjual');

    Route::post('transfer-produk', 'ShopController@transfer_produk')->name('transfer-produk');
    Route::post('hapus-produk/{product_id}/{shop_id}', 'ShopController@hapusProduk');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('getprodukkasir', [
        'uses' => 'OrderController@getproduk',
        'as' => 'ajax.get.produk.kasir',
    ]);
    Route::post('rekapPegawaiExcel', 'HomeController@rekap_pegawai_excel');
    Route::get('rekap/{id}', 'HomeController@rekapPegawai');
    Route::get('getrekapuser/{id}', 'HomeController@getRekapPegawai');
    Route::resource('orders', 'OrderController');
    Route::post('/selesai/{code}/{total}', 'OrderController@selesai');
    Route::get('/hapus-item/{id}/{code}', 'OrderController@hapusItem');
    Route::post('/submit/{code}', 'OrderController@submit');
    Route::get('/get/{id}', 'OrderController@get');
    Route::get('/reset/{id}', 'UserController@resetpassword');
    Route::post('/reset', 'UserController@change');
    Route::post('/cetak/{code}/{total}', 'OrderController@cetak');

    Route::get('ajax-get-product/{search}', 'ProductController@ajaxGetProduct');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
