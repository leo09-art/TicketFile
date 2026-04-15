<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

//connexion et inscription
Route::get('/login',[AuthController::class,"login"])->name("login");
Route::post('/login',[AuthController::class,"authenticate"])->name("login.authenticate");
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/admin/create-account', [AuthController::class, 'adminCreateAccount'])
    ->middleware('auth')
    ->name('admin.accounts.create');

//dashboard
Route::get('/dashboard-agent', function (){
    return view("pages.agent.dashboard-agent");
})->name('dashboard.agent');
Route::get('/dashboard-usager', function (){
    return view("pages.users.dashboard-user");
})->name('dashboard.usager');
Route::get('/dashboard-admin', function (){
    return view("pages.admin.dashboard-admin");
})->name('dashboard.admin');
