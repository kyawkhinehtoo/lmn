<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Menu;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;
class RoleController extends Controller
{
    public function index(Request $request)
    {
         $roles = Role::when($request->role, function($query, $pkg){
             $query->where('name','LIKE','%'.$pkg.'%');
         })
        ->paginate(10);
        $menus = Menu::all();
        $customer = new Customer();
        $col = $customer->getTableColumns();
        return Inertia::render('Setup/Role', ['roles' => $roles, 'col' => $col, 'menus'=>$menus]);

    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        $role = new Role();
        $role->name = $request->name;
       
        if(!empty($request->permission)){
            foreach ($request->permission as $key => $value) {
                if($key !== array_key_last($request->permission))
                $role->permission .= $value['name'].',';
                else
                $role->permission .= $value['name'];
            }
            
        }
        $role->read_customer = $request->read_customer;
        $role->read_incident = $request->read_incident;
        $role->write_incident = $request->write_incident;
        $role->bill_generation = $request->bill_generation;
        $role->edit_invoice = $request->edit_invoice;
        $role->bill_receipt = $request->bill_receipt;
        
        $role->save();
         return redirect()->route('role.index')->with('message', 'Role Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $role = Role::find($request->input('id'));
            $role->name = $request->name;
            if(!empty($request->permission)){
                $role->permission = '';
                foreach ($request->permission as $key => $value) {
                    if($key !== array_key_last($request->permission))
                    $role->permission .= $value['name'].',';
                    else
                    $role->permission .= $value['name'];
                }
                
            }
            // if(!empty($request->menu_id)){
            //     foreach ($request->menu_id as $key => $value) {
            //         if($key !== array_key_last($request->menu_id))
            //         $role->menu_id .= $value['id'].',';
            //         else
            //         $role->menu_id .= $value['id'];
            //     }
                
            // }
            $role->read_customer = $request->read_customer;
            $role->read_incident = $request->read_incident;
            $role->write_incident = $request->write_incident;
            $role->bill_generation = $request->bill_generation;
            $role->edit_invoice = $request->edit_invoice;
            $role->bill_receipt = $request->bill_receipt;
            
            $role->update();
            return redirect()->back()
                    ->with('message', 'Role Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Role::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
