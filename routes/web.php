<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::middleware('auth')
    ->group(static function (Router $router) {
        $router->get('/', 'HomeController')->name('home');

        $router->get('apply-loan', 'ApplyLoanController')->name('apply-loan');
        $router->post('apply-loan', 'SubmitLoanController')->name('submit-loan');
        $router->post('make-payment/{loan}', 'MakePaymentController')->name('make-payment');
    });
