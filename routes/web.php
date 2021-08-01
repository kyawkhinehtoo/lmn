<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


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
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });



Route::group(['middleware'=> 'auth'], function(){
	Route::get('/dashboard', 'DashboardController@show')->name('dashboard');
	Route::resource('/user', UserController::class);
	Route::resource('/sla', SlaController::class);
	Route::resource('/township', TownshipController::class);
	Route::resource('/equiptment', EquiptmentController::class);
	Route::get('/getpackage/{id}', 'PackageController@getBundle');
	Route::get('/incidentOverdue', 'IncidentAlertController@getOverdue');
	Route::get('/incidentRemain', 'IncidentAlertController@getRemain');
	Route::get('/getTask/{id}', 'IncidentController@getTask');
	Route::get('/getFile/{id}', 'IncidentController@getFile');
	Route::delete('/deleteFile/{id}', 'IncidentController@deleteFile');
	Route::post('/addTask', 'IncidentController@addTask');
	Route::put('/editTask/{id}', 'IncidentController@editTask');
	Route::post('/upload', 'FileController@upload')->name('upload');
	Route::resource('/package', PackageController::class);
	Route::resource('/project', ProjectController::class);
	Route::resource('/status', StatusController::class);
	Route::resource('/role', RoleController::class);
	Route::resource('/voip', VoipController::class);
});
Route::group(['middleware'=> 'auth'], function(){

	Route::resource('/customer', CustomerController::class);
	Route::post('/customer/search/', 'CustomerController@show');
	Route::resource('/incident', IncidentController::class);
	Route::get('/incidentlist', 'IncidentController@getIncident');
	Route::get('importExportView', 'ExcelController@importExportView')->name('importExportView');
	// Route for export/download tabledata to .csv, .xls or .xlsx
	Route::get('exportExcel/{type}', 'ExcelController@exportExcel')->name('exportExcel');
	// Route for import excel data to database.
	Route::post('importExcel', 'ExcelController@importExcel')->name('importExcel');
	
});

Route::middleware(['auth:sanctum', 'verified'])->get('/test', function () {
    return Inertia::render('Test');
})->name('test');
