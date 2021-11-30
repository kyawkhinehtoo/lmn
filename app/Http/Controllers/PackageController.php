<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Sla;
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
        $bundle_equiptments = BundleEquiptment::get();
        $slas = Sla::get();
        return Inertia::render('Setup/Package', ['packages' => $packages, 'bundle_equiptments' => $bundle_equiptments,'slas'=>$slas]);

    }

    public function getBundle($id){
        if($id){
            $bundles =  DB::table('package_bundles')
            ->join('packages', 'package_bundles.package_id', '=', 'packages.id')
            ->join('bundle_equiptments', 'package_bundles.bundle_equiptment_id', '=', 'bundle_equiptments.id')
            ->where('packages.id','=',$id)
            ->select('bundle_equiptments.id as id','bundle_equiptments.name as bundle_name', 'package_bundles.qty as qty', 'bundle_equiptments.detail as detail ')
            ->get();
        
            return response()->json($bundles);
        }
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'speed' => ['required'],
            'sla_id' => ['required'],
            'type' => ['required', 'in:ftth,b2b,dia'],
            'contract_period' => ['required', 'in:6,12,24'],
        ])->validate();

        $package = new Package();
        $package->name = $request->name;
        $package->speed = $request->speed;
        $package->currency = $request->currency;
        $package->type = $request->type;
        $package->status = $request->status;
        $package->sla_id = $request->sla_id;
        $package->price = $request->price;
        $package->contract_period = (string)$request->contract_period;
        $package->save();
        $id = $package->id;
        if($request->bundleList && $id){
          
          foreach ($request->bundleList as $key => $value) {
            $package_bundle = new PackageBundle();
            $package_bundle->package_id = $id;
            $package_bundle->bundle_equiptment_id = $value[0]['id'];
            $package_bundle->qty = $value[1]['qty'];
            $package_bundle->save();
          }
         
        }
      //  dd($request->bundle_equiptment);
        // Township::create($request->all());
  
         return redirect()->route('package.index')->with('message', 'Package Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'speed' => ['required'],
            'type' => ['required', 'in:ftth,b2b,dia'],
            'sla_id' => ['required'],
            'contract_period' => ['required', 'in:6,12,24'],
        ])->validate();
  
        if ($request->has('id')) {
            $package = Package::find($request->input('id'));
            $package->name = $request->name;
            $package->speed = $request->speed;
            $package->currency = $request->currency;
            $package->type = $request->type;
            $package->sla_id = $request->sla_id;
            $package->status = $request->status;
            $package->price = $request->price;
            $package->contract_period = (string)$request->contract_period;
            $package->update();
            PackageBundle::where('package_id',$request->input('id'))->delete();
            if($request->bundleList ){
                foreach ($request->bundleList as $key => $value) {
                    $package_bundle = new PackageBundle();
                    $package_bundle->package_id = $request->input('id');
                    $package_bundle->bundle_equiptment_id = $value[0]['id'];
                    $package_bundle->qty = $value[1]['qty'];
                    $package_bundle->save();
                  }
            }
            return redirect()->back()
                    ->with('message', 'Package Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Package::find($request->input('id'))->delete();
            PackageBundle::where('package_id',$request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
