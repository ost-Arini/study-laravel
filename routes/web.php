<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

//ini karena ada auth routesnya jadi otomatis nyambung
Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
Route::post('/register', ['uses'=>'Auth\RegisterController@create', 'as'=>'register']);
Route::get('/users', 'UsersController@index')->name('users')->middleware('auth');
// Route::get('/users/profile', function(){
//     return view('users/profile');
// });

//USERS
//nampilin data user di profile page
Route::get('/profile/{users}', 'UsersController@show');
//nampilin data user di edit page
Route::get('/edit/{users}', 'UsersController@profile_edit');
//nampilin data user di confirm page
Route::get('/confirm/{user_id}', 'UsersController@profile_edit');
//lempar data user ke confirm page
Route::post('/confirm/{user_id}', 'UsersController@profile_edit')->name('confirm');
//lempar data ke success page
Route::post('/success/{user_id}', 'UsersController@profile_edit_success')->name('editusersuccess');
//delete user
Route::post('/deleteuser','UsersController@confirmdelete')->name('deletesuccess');

//PRODUCTS
//submit new
Route::get('/submit', 'ProductsController@submit')->name('submitnew');
Route::post('/submitconfirm', 'ProductsController@submitconfirm')->name('submitconfirm');
Route::post('/submitsuccess', 'ProductsController@submitsuccess')->name('submitsuccess');

//all products
Route::get('/all', 'ProductsController@allproducts')->name('allproducts');
Route::post('/deleteproduct','ProductsController@confirmdeleteproduct')->name('deleteproductsuccess');

//edit product
Route::get('/editproduct/{product_id}', 'ProductsController@editproduct');
Route::get('/editconfirm/{product_id}', 'ProductsController@editproduct');
Route::post('/editconfirm/{product_id}', 'ProductsController@editproduct')->name('editconfirm');
Route::post('/editsuccess/{product_id}', 'ProductsController@editsuccess')->name('editproductsuccess');

//your products
// Route::get('/your', 'ProductsController@yourproducts')->name('yourproducts');

//homeproductdisplay
Route::get('/home', 'ProductsController@productsdisplay')->name('home');
Route::post('/home', 'ProductsController@productsdisplay');


//TRANSACTIONS
//submit new
Route::get('/submitnew','TransactionController@submittrans')->name('newtrans');
Route::post('/submittransconfirm','TransactionController@submitconfirm')->name('submittransconfirm');
Route::post('submittranssuccess','TransactionController@submittranssuccess')->name('submittranssuccess');
//all transaction
Route::get('/alltrans', 'TransactionController@display')->name('alltrans');
//transaction detail
Route::get('/detail/{transaction_id}', 'TransactionController@detail')->name('detailtrans');
Route::post('/deletetrans', 'TransactionController@delete')->name('deletetrans');
//transaction edit
Route::get('/edittrans/{transaction_id}', 'TransactionController@edit')->name('edittrans');
Route::get('/edittransconfirm/{transaction_id}', 'TransactionController@edit');
Route::post('/edittransconfirm/{transaction_id}', 'TransactionController@edit')->name('edittransconfirm');
Route::post('/edittranssuccess/{transaction_id}', 'TransactionController@editsuccess')->name('edittranssuccess');

//TYPES
Route::get('/typelist','TypesController@show')->name('typelist');
Route::post('/typelist', 'TypesController@add')->name('addtype');