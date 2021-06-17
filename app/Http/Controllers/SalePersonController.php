<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalePerson;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;
class SalePersonController extends Controller
{
    public function index(Request $request)
    {
         $sale_persons = SalePerson::when($request->sale_person, function($query, $pkg){
             $query->where('name','LIKE','%'.$pkg.'%');
         })
         ->paginate(10);
        return Inertia::render('Setup/SalePerson', ['sale_persons' => $sale_persons]);

    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        $sale_person = new SalePerson();
        $sale_person->name = $request->name;
        $sale_person->save();
         return redirect()->route('sale_person.index')->with('message', 'SalePerson Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $sale_person = SalePerson::find($request->input('id'));
            $sale_person->name = $request->name;
            $sale_person->update();
            return redirect()->back()
                    ->with('message', 'SalePerson Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            SalePerson::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
