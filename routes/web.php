<?php

use App\Http\Controllers\ExerciseVideoFileController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Portal;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/programs', [PageController::class, 'programs'])->name('programs');
Route::get('/nutrition', [PageController::class, 'nutrition'])->name('nutrition');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/checkout', [OrderController::class, 'create'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('terms-of-service');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::post('/contact', [FormController::class, 'submitContact'])->name('contact.submit');

// Client login (accounts are created by the coach when a payment is confirmed).
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Hosted exercise clips are not in public/, so these URLs reach Laravel and are access-checked.
Route::get('/videos/exercises/{file}', ExerciseVideoFileController::class)
    ->where('file', '[a-z0-9-]+\.mp4')
    ->middleware('auth')
    ->name('exercise-videos.file');

// Client coaching portal
Route::middleware(['auth', 'client'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [Portal\PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/news', [Portal\PortalController::class, 'announcements'])->name('announcements');
    Route::get('/account', [Portal\PortalController::class, 'account'])->name('account');
    Route::put('/account/password', [Portal\PortalController::class, 'updatePassword'])->name('account.password');

    Route::get('/program', [Portal\LibraryController::class, 'program'])->name('program');
    Route::get('/nutrition', [Portal\LibraryController::class, 'nutrition'])->name('nutrition');
    Route::get('/videos', [Portal\LibraryController::class, 'videos'])->name('videos');

    Route::get('/progress', [Portal\ProgressController::class, 'index'])->name('progress');
    Route::post('/progress', [Portal\ProgressController::class, 'store'])->name('progress.store');
    Route::delete('/progress/{entry}', [Portal\ProgressController::class, 'destroy'])->name('progress.destroy');

    Route::get('/notes', [Portal\NoteController::class, 'index'])->name('notes');
    Route::post('/notes', [Portal\NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [Portal\NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [Portal\NoteController::class, 'destroy'])->name('notes.destroy');

    Route::get('/support', [Portal\TicketController::class, 'index'])->name('tickets');
    Route::post('/support', [Portal\TicketController::class, 'store'])->middleware('throttle:10,1')->name('tickets.store');
    Route::get('/support/{ticket}', [Portal\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/support/{ticket}', [Portal\TicketController::class, 'reply'])->middleware('throttle:20,1')->name('tickets.reply');

    Route::get('/schedule', [Portal\ScheduleController::class, 'index'])->name('schedule');
    Route::post('/schedule/{slot}/book', [Portal\ScheduleController::class, 'book'])->name('schedule.book');
    Route::post('/schedule/{slot}/cancel', [Portal\ScheduleController::class, 'cancel'])->name('schedule.cancel');
});

// Coach admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\OrderController::class, 'index'])->name('orders');
    Route::post('/orders/{order}/grant', [Admin\OrderController::class, 'grantAccess'])->name('orders.grant');
    Route::post('/orders/{order}/cancel', [Admin\OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/clients', [Admin\ClientController::class, 'index'])->name('clients');
    Route::get('/clients/{client}', [Admin\ClientController::class, 'show'])->name('clients.show');
    Route::put('/clients/{client}/library', [Admin\ClientController::class, 'updateLibrary'])->name('clients.library');

    Route::get('/news', [Admin\AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/news', [Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/news/{announcement}', [Admin\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/news/{announcement}', [Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/tickets', [Admin\TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/{ticket}', [Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}', [Admin\TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/close', [Admin\TicketController::class, 'close'])->name('tickets.close');

    Route::get('/programs', [Admin\TrainingPlanController::class, 'index'])->name('programs');
    Route::post('/programs', [Admin\TrainingPlanController::class, 'store'])->name('programs.store');
    Route::put('/programs/{plan}', [Admin\TrainingPlanController::class, 'update'])->name('programs.update');
    Route::delete('/programs/{plan}', [Admin\TrainingPlanController::class, 'destroy'])->name('programs.destroy');

    Route::get('/nutrition', [Admin\NutritionPlanController::class, 'index'])->name('nutrition');
    Route::post('/nutrition', [Admin\NutritionPlanController::class, 'store'])->name('nutrition.store');
    Route::put('/nutrition/{plan}', [Admin\NutritionPlanController::class, 'update'])->name('nutrition.update');
    Route::delete('/nutrition/{plan}', [Admin\NutritionPlanController::class, 'destroy'])->name('nutrition.destroy');

    Route::get('/videos', [Admin\VideoController::class, 'index'])->name('videos');
    Route::post('/videos', [Admin\VideoController::class, 'store'])->name('videos.store');
    Route::put('/videos/{video}', [Admin\VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video}', [Admin\VideoController::class, 'destroy'])->name('videos.destroy');

    Route::get('/slots', [Admin\SlotController::class, 'index'])->name('slots');
    Route::post('/slots', [Admin\SlotController::class, 'store'])->name('slots.store');
    Route::delete('/slots/{slot}', [Admin\SlotController::class, 'destroy'])->name('slots.destroy');
});
