<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackageBundle;
use App\Models\BundleEquiptment;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;
class PackageController extends Controller
{
    public function index(Request $request)
    {
         $packages = Package::when($request->package, function($query, $pkg){
             $query->where('name','LIKE','%'.$pkg.'%');
         })
         ->paginate(10);
       // $packages = PackageBundle::with(['package','bundleEquiptment'])->get();
  
        // foreach($packages as $package){
        //     echo $package->package->name.'<br />';
        //     echo $package->bundleEquiptment->name.', QTY :'. $package->qty.'<br />'; 
        // }
        
        return Inertia::render('Setup/Package', ['packages' => $packages]);

    }

    public function getBundle($id){
        if($id){
            $bundles =  DB::table('package_bundles')
            ->join('packages', 'package_bundles.package_id', '=', 'packages.id')
            ->join('bundle_equiptments', 'package_bundles.bundle_equiptment_id', '=', 'bundle_equiptments.id')
            ->where('packages.id','=',$id)
            ->select('bundle_equiptments.name as bundle_name', 'bundle_equiptments.detail as detail ')
            ->get();
            return response()
            ->json($bundles);
        }
    }

    public function store(Request $request)
    {

    }
    public function update(Request $request, $id)
    {
        
    }
    public function destroy(Request $request, $id)
    {
        
    }
}
