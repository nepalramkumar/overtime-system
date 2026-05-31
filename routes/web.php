<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MasterSettingsController;
use App\Http\Controllers\EventController;

// स्वागत पेज
Route::get('/', function () {
    return view('welcome');
});

// ड्यासबोर्ड (यो मात्र Auth आवश्यक पर्ने बनाउन सकिन्छ)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// प्रोफाइलका रुटहरू (Auth आवश्यक)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// auth.php समावेश गर्नुहोस्
require __DIR__.'/auth.php';

// ==========================================
// यहाँ तलका सबै रुटहरू लगइन बिना चल्नेछन्
// ==========================================

Route::get('/overtime/create', [OvertimeController::class, 'create'])->name('overtime.create');
Route::post('/overtime/store', [OvertimeController::class, 'store'])->name('overtime.store');
Route::get('/events/list', [OvertimeController::class, 'eventList'])->name('events.list');
Route::get('/overtime', [OvertimeController::class, 'index'])->name('overtime.list');
Route::get('/overtime/{id}/edit', [OvertimeController::class, 'edit'])->name('overtime.edit');
Route::put('/overtime/{id}', [OvertimeController::class, 'update'])->name('overtime.update');
Route::delete('/overtime/{id}', [OvertimeController::class, 'destroy'])->name('overtime.destroy');

Route::resource('employees', EmployeeController::class);


Route::get('/settings', [MasterSettingsController::class, 'index'])->name('settings.index');
Route::put('/settings/allowance/{id}', [MasterSettingsController::class, 'updateAllowance'])->name('settings.updateAllowance');
Route::post('/settings/allowance/store', [MasterSettingsController::class, 'storeAllowance'])->name('settings.storeAllowance');
Route::delete('/settings/allowance/delete/{id}', [MasterSettingsController::class, 'destroyAllowance'])->name('settings.destroyAllowance');

Route::get('/settings/snack', [MasterSettingsController::class, 'snackIndex'])->name('settings.snack');

Route::get('/settings/shifts', [MasterSettingsController::class, 'shiftIndex'])->name('shifts.index');
Route::post('/settings/shifts/store', [MasterSettingsController::class, 'shiftStore'])->name('shifts.store');
Route::put('/settings/shifts/update/{id}', [MasterSettingsController::class, 'shiftUpdate'])->name('shifts.update');
Route::delete('/settings/shifts/delete/{id}', [MasterSettingsController::class, 'shiftDestroy'])->name('shifts.destroy');

Route::get('/events/list', [EventController::class, 'index'])->name('events.list');
Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
use App\Http\Controllers\UserController;

Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::get('/reports', [OvertimeController::class, 'generateReport'])->name('reports.index');


