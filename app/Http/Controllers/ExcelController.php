<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Exports\CustomersExport;
use App\Imports\CustomersImport;
use App\Imports\ContractUpdate;
use App\Imports\PaymentImport;

use App\Exports\BillingExport;
use App\Exports\TempBillingExport;
use App\Exports\RevenueExport; 
use App\Imports\CustomersUpdate;
use App\Imports\TempBillingUpdate;
use Excel;
use Storage;
use Illuminate\Support\Facades\Session;
class ExcelController extends Controller
{
    public function importExportView()
    {
       return view('excel.index');
    }
    public function paymentImportView()
    {
       return view('excel.payment');
    }
    public function tempImportView()
    {
       return view('excel.tempupdate');
    }
    public function updateContractView()
    {
       return view('excel.contractupdate');
    }
   
   
    /**
    * @return \Illuminate\Support\Collection
    */
    public function exportExcel(Request $request) 
    {
        //$type = xlsx, xls etc.
     //   return (new CustomersExport(($request)));
     return (new CustomersExport($request))->download('customers.csv');
       // return Excel::download(new CustomersExport($request), 'customers.csv');
    }
    public function exportBillingExcel(Request $request) 
    { 
     return (new BillingExport($request))->download('billings.csv');
    }
    public function exportTempBillingExcel(Request $request) 
    { 
     return (new TempBillingExport($request))->download('temp_billings.csv');
    }
    public function exportRevenue(Request $request) 
    { 
     return (new RevenueExport($request))->download('revenue.csv');
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function importExcel(Request $request) 
    {
        Excel::import(new CustomersImport,$request->import_file);

        Session::put('success', 'Your file is imported successfully in database.');
           
        return back();
    }
    public function updateExcel(Request $request) 
    {
        Storage::prepend('import_log.log', 'Importing Excel File');
        Excel::import(new CustomersUpdate,$request->import_file);

        Session::put('success', 'Your file is imported successfully in database.');
           
        return back();
    }
    public function updateContract(Request $request) 
    {
        Storage::prepend('contract_update.log', 'Importing Excel File');
        Excel::import(new ContractUpdate,$request->import_file);

        Session::put('success', 'Your file is imported successfully in database.');
           
        return back();
    }
    public function updateTemp(Request $request) 
    {
        Storage::prepend('import_log.log', 'Importing Excel File');
        Excel::import(new TempBillingUpdate,$request->import_file);

        Session::put('success', 'Your file is imported successfully in database.');
           
        return back();
    }
    public function importPayment(Request $request) 
    {
        Excel::import(new PaymentImport,$request->import_file);

        Session::put('success', 'Your file is imported successfully in database.');
           
        return back();
    }
}
