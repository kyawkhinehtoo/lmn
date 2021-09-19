<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Exports\CustomersExport;
use App\Imports\CustomersImport;
use Excel;
use Illuminate\Support\Facades\Session;
class ExcelController extends Controller
{
    public function importExportView()
    {
       return view('excel.index');
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
   
    /**
    * @return \Illuminate\Support\Collection
    */
    public function importExcel(Request $request) 
    {
        Excel::import(new CustomersImport,$request->import_file);

        Session::put('success', 'Your file is imported successfully in database.');
           
        return back();
    }
}
