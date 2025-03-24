<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'App\Api\v1\Controllers'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('users', ['uses' => 'UserController@index']);
    });
});

Route::group(['prefix'=>'/clients'],function(){
    Route::get('/',[\App\Api\controller\ClientController::class, 'getAllClients'])->name('api.client.getAll');
    Route::get('/{id}',[\App\Api\controller\ClientController::class,'getClientById'])->name('api.client.getById');
    Route::get('/total',[\App\Api\controller\ClientController::class, 'total'])->name('api.client.total');
});

Route::group(['prefix'=>'/projets'],function(){
    Route::get('/',[\App\Api\controller\ProjetController::class, 'getAllProjets'])->name('api.projets.getAll');
    Route::get('/{id}',[\App\Api\controller\ProjetController::class,'getById'])->name('api.projets.getById');

});

Route::group(['prefix'=>'/tasks'],function(){
    Route::get('/',[\App\Api\controller\TaskController::class, 'getAllTasks'])->name('api.tasks.getAll');
    Route::get('/{id}',[\App\Api\controller\TaskController::class,'getById'])->name('api.tasks.getById');
});

Route::group(['prefix'=>'/offers'],function(){
    Route::get('/',[\App\Api\controller\OfferController::class, 'getAllOffers'])->name('api.offers.getAll');
    Route::get('/{id}',[\App\Api\controller\OfferController::class,'getById'])->name('api.offers.getById');
    Route::get('/status/won',[\App\Api\controller\OfferController::class,'getWon'])->name('api.offers.getWon');
    Route::get('/status/progress',[\App\Api\controller\OfferController::class,'getInProgess'])->name('api.offers.getInProgress');
    Route::get('/status/lost',[\App\Api\controller\OfferController::class,'getLost'])->name('api.offers.getLost');
    Route::get('/count',[\App\Api\controller\OfferController::class, 'countOffer'])->name('api.offers.count');

});

Route::group(['prefix'=>'/invoices'],function(){
    Route::get('/',[\App\Api\controller\InvoiceController::class, 'getAllInvoice'])->name('api.invoice.getAll');
    Route::get('/{id}',[\App\Api\controller\InvoiceController::class,'getById'])->name('api.invoice.getById');
});

Route::group(['prefix'=>'/payments'],function(){
    Route::get('/',[\App\Api\controller\PaymentController::class, 'getAllPayment'])->name('api.payment.getAll');
    Route::get('/{id}',[\App\Api\controller\PaymentController::class,'getById'])->name('api.payment.getById');
});

Route::group(['prefix'=>'/users'],function(){
    Route::get('/',[\App\Api\controller\UsersController::class, 'getAllUsers'])->name('api.users.getAll');
    Route::get('/email/{email}',[\App\Api\controller\UsersController::class,'getByMail'])->name('api.users.getByMail');
});
//Route::get('users',[\App\Api\controller\ClientController::class, 'getAllClients'])->name('test');

