<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PromoController;
use Illuminate\Support\Facades\Route;


// ==========================
// Home
// ==========================
Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/movies/active', [MovieController::class, 'homeMovies'])->name('home.movies.all');


// ==========================
// Schedule Detail
// ==========================
Route::get('/schedules/detail/{movie_id}', [MovieController::class, 'movieSchedule'])->name('schedules.detail');


// ==========================
// AUTH GUEST ONLY
// ==========================
Route::middleware('isGuest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/signup', function () {
        return view('auth.signup');
    })->name('signup');

    Route::post('/signup', [UserController::class, 'register'])->name('signup.register');
    Route::post('/auth', [UserController::class, 'authentication'])->name('auth');
});


// ==========================
// AUTH GLOBAL
// ==========================
Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');


// ==========================
// LOGIN REQUIRED / USER
// ==========================
Route::middleware('isUser')->group(function () {

    // Seats
    Route::get('/schedules/{scheduleId}/hours/{hourId}', [ScheduleController::class, 'showSeats'])
        ->name('schedules.show_seats');

    // Tickets
    Route::prefix('/tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::post('/', [TicketController::class, 'store'])->name('store');

        Route::get('/{ticketId}/order', [TicketController::class, 'orderPage'])->name('order');

        Route::post('/qrcode', [TicketController::class, 'createQrcode'])->name('qrcode');

        Route::get('/{ticketId}/payment', [TicketController::class, 'paymentPage'])->name('payment');
        Route::patch('/{ticketId}/payment/status', [TicketController::class, 'updateStatusPayment'])->name('payment.status');
        
        // Route untuk preview tiket (bukti pembayaran)
        Route::get('/{ticketId}/payment/proof', [TicketController::class, 'proofPayment'])->name('payment.proof');
        
        // Route untuk download PDF
        Route::get('/{ticketId}/pdf', [TicketController::class, 'exportPdf'])->name('export_pdf');
    });
});


// ==========================
// Cinemas Public
// ==========================
Route::get('/cinemas/list', [CinemaController::class, 'cinemaList'])->name('cinemas.list');
Route::get('/cinemas/{cinema_id}/schedules', [CinemaController::class, 'cinemaSchedule'])->name('cinemas.schedules');


// ==========================
// ADMIN SECTION
// ==========================
Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function(){

    Route::get('/tickets/chart',[TicketController::class,'chart'])->name('tickets.chart');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Cinemas
    Route::prefix('cinemas')->name('cinemas.')->group(function () {
        Route::get('/index', [CinemaController::class, 'index'])->name('index');
        Route::get('/create', [CinemaController::class, 'create'])->name('create');
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CinemaController::class,'edit'])->name('edit');
        Route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        Route::get('/export', [CinemaController::class, 'export'])->name('export');
        Route::get('/trash', [CinemaController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        Route::delete('/deletepermanen/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [CinemaController::class,'datatables'])->name('datatables');
    });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('istaf');
        Route::get('/create', [UserController::class, 'create'])->name('cstaf');
        Route::post('/store', [UserController::class, 'store'])->name('sstaf');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('estaf');   
        Route::put('/update/{id}', [UserController::class, 'update'])->name('ustaf');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('dstaf');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::delete('/deletepermanen/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [UserController::class,'datatables'])->name('datatables');
    });

    // Movies
    Route::prefix('movies')->name('movies.')->group(function(){
        Route::get('/chart', [MovieController::class, 'chart'])->name('chart');
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MovieController::class, 'destroy'])->name('delete');
        Route::patch('/nonaktif/{id}',[MovieController::class,'nonaktif'])->name('nonaktif');
        Route::get('/export', [MovieController::class, 'export'])->name('export');
        Route::get('/trash', [MovieController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        Route::delete('/deletepermanen/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [MovieController::class,'datatables'])->name('datatables');
    });

});


// ==========================
// STAFF SECTION (WITH MIDDLEWARE)
// ==========================
Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function() {

    Route::get('/dashboard', function(){
        return view('staff.dashboard');
    })->name('dashboard');

    // Schedules
    Route::prefix('schedules')->name('schedules.')->group(function(){
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        Route::get('/trash', [ScheduleController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/deletepermanen/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/export', [ScheduleController::class, 'export'])->name('export');
        Route::get('/datatables', [ScheduleController::class,'datatables'])->name('datatables');
    });

    // Promos
    Route::prefix('promos')->name('promo.')->group(function(){
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
        Route::get('/export', [PromoController::class, 'export'])->name('export');
        Route::get('/trash', [PromoController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        Route::delete('/deletepermanen/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [PromoController::class,'datatables'])->name('datatables');
    });
});