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

Route::redirect('/', '/dashboard');

Route::group(['middleware'=> ['auth','role']], function(){
	
	Route::resource('/user', UserController::class);
	Route::resource('/sla', SlaController::class);
	Route::resource('/port', PortController::class);
	Route::resource('/snport', SNPortController::class);
	Route::get('/generateSN', 'SNPortController@generateSN');
	Route::delete('/snport/group/{id}', 'SNPortController@deleteGroup');
	Route::delete('/port/group/{id}', 'PortController@deleteGroup');
	Route::resource('/township', TownshipController::class);
	Route::resource('/equiptment', EquiptmentController::class);
	Route::resource('/menu', MenuController::class);
	Route::resource('/package', PackageController::class);
	Route::resource('/project', ProjectController::class);
	Route::resource('/status', StatusController::class);
	Route::resource('/role', RoleController::class);
	Route::resource('/voip', VoipController::class);
});
Route::group(['middleware'=> 'auth'], function(){
	Route::get('/getpackage/{id}', 'PackageController@getBundle');
	Route::get('/incidentOverdue', 'IncidentAlertController@getOverdue');
	Route::get('/incidentRemain', 'IncidentAlertController@getRemain');
	Route::get('/getTask/{id}', 'IncidentController@getTask');
	Route::get('/getLog/{id}', 'IncidentController@getLog');
	Route::get('/getHistory/{id}', 'IncidentController@getHistory');
	Route::get('/getFile/{id}', 'IncidentController@getFile');
	Route::get('/getCustomerHistory/{id}', 'CustomerController@getHistory');
	Route::get('/getCustomerFile/{id}', 'IncidentController@getCustomerFile');
	Route::delete('/deleteFile/{id}', 'IncidentController@deleteFile');
	Route::post('/addTask', 'IncidentController@addTask');
	Route::put('/editTask/{id}', 'IncidentController@editTask');
	Route::post('/uploadData', 'FileController@upload')->name('upload');
	Route::post('/getMenu', 'MenuController@getMenu');
	Route::get('/getDnId/{name}', 'PortController@getIdByName');
	Route::get('/dashboard', 'DashboardController@show')->name('dashboard');
	Route::resource('/customer', CustomerController::class);
	Route::post('/customer/search/', 'CustomerController@show');
	Route::resource('/incident', IncidentController::class);
	Route::get('/incidentlist', 'IncidentController@getIncident');

	Route::get('importExportView', 'ExcelController@importExportView')->name('importExportView');
	// Route for export/download tabledata to .csv, .xls or .xlsx
	Route::post('/exportExcel', 'ExcelController@exportExcel')->name('exportExcel');
	// Route for import excel data to database.
	Route::post('importExcel', 'ExcelController@importExcel')->name('importExcel');
	Route::post('updateExcel', 'ExcelController@updateExcel')->name('updateExcel');


	Route::resource('/port', PortController::class);
	Route::resource('/snport', SNPortController::class);
	Route::get('/generateSN', 'SNPortController@generateSN');
	Route::delete('/snport/group/{id}', 'SNPortController@deleteGroup');
	Route::delete('/port/group/{id}', 'PortController@deleteGroup');
	

	//Billing 
	Route::get('tempImportView', 'ExcelController@tempImportView')->name('tempImportView');

	Route::get('updateContractView', 'ExcelController@updateContractView')->name('updateContractView');
	Route::get('updateTownshipView', 'ExcelController@updateTownshipView')->name('updateTownshipView');
	Route::post('updateContract', 'ExcelController@updateContract')->name('updateContract');
	Route::post('updateTownship', 'ExcelController@updateTownship')->name('updateTownship');
	
	Route::post('updateTempExcel', 'ExcelController@updateTemp')->name('updateTempExcel');
	Route::post('importPayment', 'ExcelController@importPayment')->name('importPayment');


	Route::post('/exportBillingExcel', 'ExcelController@exportBillingExcel')->name('exportBillingExcel');
	Route::post('/exportTempBillingExcel', 'ExcelController@exportTempBillingExcel')->name('exportTempBillingExcel');
	Route::post('/exportRevenue', 'ExcelController@exportRevenue')->name('exportRevenue');
	
	Route::get('/billGenerator', 'BillingController@BillGenerator')->name('billGenerator');
	Route::post('/updateTemp', 'BillingController@updateTemp')->name('updateTemp');
	Route::post('/updateInvoice', 'BillingController@updateInvoice')->name('updateInvoice');
	Route::post('/createInvoice', 'BillingController@createInvoice')->name('createInvoice');
	Route::post('/doGenerate', 'BillingController@doGenerate');
	Route::post('/saveFinal', 'BillingController@saveFinal');
	//Route::post('/showbill', 'BillingController@showBill')->name('showbill');
	Route::get('/showbill', 'BillingController@showBill')->name('showbill');
	Route::get('/tempBilling', 'BillingController@goTemp')->name('tempBilling');
	Route::post('/tempBilling/search/', 'BillingController@goTemp');
	Route::post('/truncateBilling', 'BillingController@destroyall');
	Route::resource('/billing', BillingController::class);

	//Billing PDF
	Route::get('/pdfpreview1/{id}', 'BillingController@preview_1');
	Route::get('/pdfpreview2/{id}', 'BillingController@preview_2');
	Route::get('/ReceiptTemplate/{id}', 'ReceiptController@template');

	Route::post('/getSinglePDF/{id}', 'BillingController@makeSinglePDF');
	Route::post('/getReceiptPDF/{id}', 'ReceiptController@makeReceiptPDF');
	Route::post('/sendSingleEmail/{id}', 'BillingController@sendSingleEmail');
	Route::post('/getAllPDF', 'BillingController@makeAllPDF');
	Route::post('/sendAllEmail', 'BillingController@sendAllEmail');

	
	//Billing Receipt
	Route::post('/saveReceipt', 'ReceiptController@store');
	Route::post('/receipt/search', 'ReceiptController@show');
	Route::resource('/receipt', ReceiptController::class);
	Route::get('/saveSingle', 'BillingController@saveSingle');
	Route::get('/runSummery', 'ReceiptController@runReceiptSummery');
	Route::get('/gettclmax', 'CustomerController@gettclmaxid');
	Route::get('/getmkmax', 'CustomerController@getmkmaxid');


	//Email Template
	Route::resource('/template', EmailTemplateController::class);


	//test
	Route::get('/testCustomer', 'CustomerController@preg_test');
	//Service Request
	Route::resource('/servicerequest', ServiceRequestController::class);

});

Route::middleware(['auth:sanctum', 'verified'])->get('/test', function () {
    return Inertia::render('Test');
})->name('test');
Route::get('/s/{shortURLKey}', '\AshAllenDesign\ShortURL\Controllers\ShortURLController');