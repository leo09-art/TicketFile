<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\UserController;

Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::get('/login',    [AuthController::class, 'login'])->name('login');
Route::post('/login',   [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register',[AuthController::class, 'store'])->name('register.store');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Création de compte par admin
Route::get('/admin/create-account', [AuthController::class, 'adminCreateAccount'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.accounts.create');

// Tickets publics (sans auth obligatoire)
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets',       [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{ticket}',[TicketController::class, 'show'])->name('tickets.show');

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [TicketController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/dashboard/data', [TicketController::class, 'adminDashboardData'])->name('dashboard.data');

    Route::get('/services',              [ServiceController::class, 'index'])->name('services');
    Route::post('/services',             [ServiceController::class, 'store'])->name('services.store');
    Route::patch('/services/{service}',  [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/counters',              [CounterController::class, 'index'])->name('counters');
    Route::post('/counters',             [CounterController::class, 'store'])->name('counters.store');
    Route::patch('/counters/{counter}',  [CounterController::class, 'update'])->name('counters.update');
    Route::delete('/counters/{counter}', [CounterController::class, 'destroy'])->name('counters.destroy');
    Route::patch('/counters/{counter}/toggle', [CounterController::class, 'toggle'])->name('counters.toggle');

    Route::get('/users',           [UserController::class, 'index'])->name('users');
    Route::patch('/users/{user}',  [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Agent
Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/dashboard',                      [TicketController::class, 'agentDashboard'])->name('dashboard');
    Route::get('/dashboard/data',                 [TicketController::class, 'agentDashboardData'])->name('dashboard.data');
    Route::post('/counter/{counter}/call-next',   [TicketController::class, 'callNext'])->name('call-next');
    Route::patch('/ticket/{ticket}/treated',      [TicketController::class, 'markTreated'])->name('ticket.treated');
    Route::patch('/ticket/{ticket}/absent',       [TicketController::class, 'markAbsent'])->name('ticket.absent');
    Route::patch('/ticket/{ticket}/recall',       [TicketController::class, 'recallTicket'])->name('ticket.recall');
});

// Usager
Route::prefix('usager')->name('usager.')->middleware(['auth', 'role:usager'])->group(function () {
    Route::get('/dashboard',               [TicketController::class, 'usagerDashboard'])->name('dashboard');
    Route::get('/dashboard/data',          [TicketController::class, 'usagerDashboardData'])->name('dashboard.data');
    Route::post('/ticket/take',            [TicketController::class, 'take'])->name('ticket.take');
    Route::get('/ticket/{ticket}',         [TicketController::class, 'ticketSuivi'])->name('ticket');
    Route::get('/ticket/{ticket}/data',    [TicketController::class, 'ticketSuiviData'])->name('ticket.data');
    Route::patch('/ticket/{ticket}/cancel',[TicketController::class, 'cancel'])->name('ticket.cancel');
});

// Redirections compatibilité (AuthController redirige vers ces noms)
Route::get('/dashboard-admin',  fn() => redirect()->route('admin.dashboard'))->name('dashboard.admin');
Route::get('/dashboard-agent',  fn() => redirect()->route('agent.dashboard'))->name('dashboard.agent');
Route::get('/dashboard-usager', fn() => redirect()->route('usager.dashboard'))->name('dashboard.usager');
