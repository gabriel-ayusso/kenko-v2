<?php

use App\Http\Controllers\AccountCycleController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\Api\BookingController as ApiBookingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ContaAzulController;
use App\Http\Controllers\EmployeeAvailabilityController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeUnavailabilityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerBookingController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes(['register' => false]);

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration.index');
Route::post('/configuration/adjustTransactionCycles', [ConfigurationController::class, 'adjustTransactionCycles']);

Route::get('/employees/welcome', [EmployeeController::class , 'welcome'])->middleware('auth')->name('employees.welcome');
Route::get('/employees/{employee}/weekly', [EmployeeController::class, 'weekly'])->middleware('auth')->name('employees.weekly');
Route::get('/employees/{employee}/booking', [EmployeeController::class, 'booking'])->middleware('auth')->name('employees.booking');
Route::post('/employees/{employee}/booking', [EmployeeController::class, 'bookingStore'])->middleware('auth')->name('employees.booking-store');
Route::resource('/employees', EmployeeController::class)->middleware('auth');
Route::get('/employees/{employee}/special-conditions', [EmployeeController::class, 'specialConditions'])->name('employees.special-conditions')->middleware('auth');
Route::put('/employees/{employee}/special-conditions', [EmployeeController::class, 'specialConditionsUpdate'])->name('employees.special-conditions-update')->middleware('auth');
Route::post('/employees/{employee}/avatar', [EmployeeController::class, 'storeAvatar'])->name('employees.avatar')->middleware('auth');
Route::get('/employees/{employee}/avatar',  [EmployeeController::class, 'getAvatar'])->name('employees.avatar');
Route::resource('/employees/{employee}/availabilities', EmployeeAvailabilityController::class)->middleware('auth');
Route::resource('/employees/{employee}/unavailabilities', EmployeeUnavailabilityController::class)->middleware('auth');

Route::resource('/services', ServiceController::class)->middleware('auth');
Route::post('/services/{service}/avatar', [ServiceController::class, 'storeAvatar'])->name('services.avatar')->middleware('auth');
Route::get('/services/{service}/avatar', [ServiceController::class, 'getAvatar'])->name('services.avatar');

Route::resource('/calendars', CalendarController::class)->middleware('auth');

Route::patch('/users/{user}/password/reset', [UserController::class, 'passwordReset'])->name('users.password-reset')->middleware('auth');
Route::resource('/users', UserController::class)->middleware('auth');

Route::resource('/bookings', BookingController::class);
Route::get('/bookings/{service}/book', [BookingController::class, 'guestBooking']);
Route::post('/bookings/{service}/book', [BookingController::class, 'guestBookingConfirm'])->name('booking.guestBookingConfirm');
Route::post('/bookings/create/step2', [BookingController::class, 'createStep2'])->name('bookings.createStep2');
Route::post('/bookings/create/step3', [BookingController::class, 'createStep3'])->name('bookings.createStep3');
Route::patch('/bookings/{booking}/update-status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus')->middleware('auth');
Route::get('/bookings/{booking}/confirm/{token}', [BookingController::class, 'confirm'])->name('bookings.confirm');
Route::get('/booking/{service}/available', [BookingController::class, 'myAvailableTime'])->middleware('auth');

Route::get('login/facebook', [LoginController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('login/facebook/callback', [LoginController::class, 'handleFacebookCallback']);
Route::get('login/instagram', [LoginController::class, 'redirectToInstagram'])->name('instagram.login');
Route::get('login/instagram/callback', [LoginController::class, 'handleInstagramCallback']);

Route::resource('/cycles', AccountCycleController::class)->middleware('auth');
Route::resource('/categories', ServiceCategoryController::class)->middleware('auth');

Route::resource('/products', ProductController::class)->middleware('auth');

Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard')->middleware('auth');
Route::get('/manager/todaySummary', [ManagerController::class, 'todaySummary'])->name('manager.todaysummary')->middleware('auth');
Route::get('/manager/tomorrowSummary', [ManagerController::class, 'tomorrowSummary'])->name('manager.tomorrowsummary')->middleware('auth');
Route::get('/manager/invoice', [ManagerController::class, 'invoice'])->name('manager.invoice')->middleware('auth');
Route::get('/manager/weekly', [ManagerController::class, 'weekly'])->name('manager.weekly')->middleware('auth');
Route::get('/manager/nextday', [ManagerController::class, 'nextday'])->name('manager.nextday')->middleware('auth');
Route::get('/manager/customer', [ManagerController::class, 'customer'])->name('manager.customer')->middleware('auth');
Route::get('/manager/weekly2', [ManagerController::class, 'weekly2'])->name('manager.weekly2')->middleware('auth');
Route::get('/manager/{booking}/pay', [ManagerController::class, 'pay'])->name('manager.pay')->middleware('auth');

Route::get('/conta-azul', [ContaAzulController::class, 'index'])->name('conta-azul.index')->middleware('auth');
Route::get('/conta-azul/login-callback', [ContaAzulController::class, 'loginCallback'])->name('conta-azul.login-callback');
Route::get('/conta-azul/refresh-token', [ContaAzulController::class, 'refreshToken'])->name('conta-azul.refresh-token')->middleware('auth');
Route::get('/conta-azul/import-services', [ContaAzulController::class, 'importServices'])->name('conta-azul.import-services')->middleware('auth');
Route::get('/conta-azul/import-customers', [ContaAzulController::class, 'importCustomers'])->name('conta-azul.import-customers')->middleware('auth');
Route::get('/conta-azul/import-banks', [ContaAzulController::class, 'importBanks'])->name('conta-azul.import-banks')->middleware('auth');
Route::get('/conta-azul/sales', [ContaAzulController::class, 'sales'])->name('conta-azul.sales')->middleware('auth');
Route::post('/conta-azul/sales', [ContaAzulController::class, 'salesPost'])->name('conta-azul.sales')->middleware('auth');
Route::post('/conta-azul/sync-bookings', [ContaAzulController::class, 'syncBookings'])->name('conta-azul.sync-bookings')->middleware('auth');

Route::resource('/transactions', AccountTransactionController::class)->middleware('auth');
Route::resource('/manager/booking', ManagerBookingController::class)->middleware('auth');

/**
 * Booking React
 */
Route::get('/api/booking/services', [ApiBookingController::class, 'services']);
Route::get('/api/booking/categories', [ApiBookingController::class, 'categories']);
Route::get('/api/booking/{service}/available', [ApiBookingController::class, 'available']);
Route::post('/api/booking', [ApiBookingController::class, 'store']);
Route::get('/api/booking/search', [ApiBookingController::class, 'search'])->middleware('auth');
