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

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', function () {
    return redirect('/get-all-products');
});


Route::get('/chat', function () {
    return view('chat');
})->middleware('auth');

Route::get('/get-all-products', function () {
    return view('products');
})->middleware('auth');

Auth::routes();
//Route::get('/home', 'HomeController@index');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/messages', 'MessagesController@getMessages');
Route::post('/messages', 'MessagesController@saveMessages');

Route::get('/products', 'CsvController@getProducts');
Route::post('/upload_csv', 'CsvController@csvUpload');
Route::get('/download_csv', 'CsvController@csvDownload');
