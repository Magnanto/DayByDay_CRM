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
    Route::get('/count',[\App\Api\controller\ProjetController::class, 'count'])->name('api.projets.count');
});

Route::group(['prefix'=>'/tasks'],function(){
    Route::get('/',[\App\Api\controller\TaskController::class, 'getAllTasks'])->name('api.tasks.getAll');
    Route::get('/count',[\App\Api\controller\TaskController::class, 'count'])->name('api.tasks.count');
    Route::get('/{id}',[\App\Api\controller\TaskController::class,'getById'])->name('api.tasks.getById');
});

Route::group(['prefix'=>'/offers'],function(){
    Route::get('/',[\App\Api\controller\OfferController::class, 'getAllOffers'])->name('api.offers.getAll');
    Route::get('/status/won',[\App\Api\controller\OfferController::class,'getWon'])->name('api.offers.getWon');
    Route::get('/status/progress',[\App\Api\controller\OfferController::class,'getInProgess'])->name('api.offers.getInProgress');
    Route::get('/status/lost',[\App\Api\controller\OfferController::class,'getLost'])->name('api.offers.getLost');
    Route::get('/count',[\App\Api\controller\OfferController::class, 'countOffer'])->name('api.offers.count');
    Route::get('/{id}',[\App\Api\controller\OfferController::class,'getById'])->name('api.offers.getById');

});

Route::group(['prefix'=>'/invoices'],function(){
    Route::get('/',[\App\Api\controller\InvoiceController::class, 'getAllInvoice'])->name('api.invoice.getAll');
    Route::get('/statues',[\App\Api\controller\InvoiceController::class,'getInvoiceStatus'])->name('api.invoice.getInvoiceStatus');
    Route::get('/turnover',[\App\Api\controller\InvoiceController::class,'getMontantInvoiceMensuelle'])->name('api.invoice.getTurnOver');
    Route::get('/{id}',[\App\Api\controller\InvoiceController::class,'getById'])->name('api.invoice.getById');
});

Route::group(['prefix'=>'/payments'],function(){
    Route::get('/',[\App\Api\controller\PaymentController::class, 'getAllPayment'])->name('api.payment.getAll');
    Route::get('/{id}',[\App\Api\controller\PaymentController::class,'getById'])->name('api.payment.getById');
    Route::get('/{id}/invoice',[\App\Api\controller\PaymentController::class,'getPaymentInvoice'])->name('api.payment.getPaymentInvoice');
    Route::post('/update/{id}',[\App\Api\controller\PaymentController::class,'update'])->name('api.payment.updatePayment');
    Route::get('delete/{id}',[\App\Api\controller\PaymentController::class,'delete'])->name('api.payment.deletePayment');
});

Route::group(['prefix'=>'/users'],function(){
    Route::get('/',[\App\Api\controller\UsersController::class, 'getAllUsers'])->name('api.users.getAll');
    Route::get('/email/{email}',[\App\Api\controller\UsersController::class,'getByMail'])->name('api.users.getByMail');
    Route::post('/auth', [\App\Http\Controllers\AuthController::class, 'logins']);
    Route::get('/{id}',[\App\Api\controller\UsersController::class, 'getById'])->name('api.users.getById');
});

Route::group(['prefix'=>'/leads'],function() {
    Route::get('/date/month/now',[\App\Api\controller\LeadsController::class, 'getAllThisMonth'])->name('api.leads.getAll');
});

Route::group(['prefix'=>'/remises'],function() {
    Route::get('/',[\App\Api\controller\RemiseControler::class, 'getOne'])->name('api.remise.getOne');
    Route::post('/update',[\App\Api\controller\RemiseControler::class, 'update'])->name('api.remise.update');
});

Route::get("/dashboard",[\App\Api\controller\DashboardController::class, 'getTotals'])->name('api.dashboard.getTotals');
//Route::get('users',[\App\Api\controller\ClientController::class, 'getAllClients'])->name('test');
Route::get("/status/{id}",[\App\Api\controller\StatusController::class, 'getById'])->name('api.status.getById');

