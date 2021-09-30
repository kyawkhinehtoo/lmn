<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\DnPorts;
use App\Models\SnPorts;
use Illuminate\Support\Facades\Storage;
class CustomersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!empty($row[0]) && trim($row[0]!="Customer ID")){
            //for DN Import
            // $dn = DnPorts::where('name','=',$row[0])->first();
            // if(!$dn){
            //     return new DnPorts([
            //         'name'=>trim($row[0])
            //     ]);
            // }
            
            //for SN Import Original
            // $dn = DnPorts::where('name','=',$row[0])->first();
            // if($dn){
            //       $j = 16;
            //     for ($i=1; $i <= $j; $i++) { 
            //     $sn =  new SnPorts();
            //         $sn->name= trim($row[1]);
            //         $sn->port   = $i;
            //         $sn->dn_id = $dn->id;
            //         $sn->save();
                
            //     }
            // }

            // for SN Import New Method
            // $dn = DnPorts::where('name','=',$row[0])->first();
            // if($dn){
            //     return new SnPorts([
            //         'name'=>trim($row[1]),
            //         'dn_id'=>$dn->id
            //     ]);
            // }

            //for Customer Import
            $dn_sn = DnPorts::join('sn_ports','sn_ports.dn_id','=','dn_ports.id')
            ->where('dn_ports.name','=',trim($row[1]))
            ->where('sn_ports.name','=',trim($row[2]))
            ->select('dn_ports.id as dn_id','sn_ports.id as sn_id')
            ->first();
            if($dn_sn){
                $cus = Customer::where('ftth_id','=',trim($row[0]))->first();
                if($cus){
                    $customer = Customer::find($cus->id);
                    $customer->sn_id = $dn_sn->sn_id;
                    $customer->update();
                }else{
                    // Storage::prepend('NoCustomer.log', 'Missing Customers');

                    Storage::append('NoCustomer.log', $row[0].',');
                echo 'no customer'.$row[0].',';
                }
            }else{
                // Storage::prepend('NoSN.log', 'Missing SNDN');

                    Storage::append('NoSN.log', $row[0].','.$row[1].','.$row[2].',');
            }
        }

            // $j = $row[1];
            // for ($i=1; $i <= $j; $i++) { 
            //     $dn =  new DnPorts();
            //         $dn->name= trim($row[0]);
            //         $dn->port   = $i;
            //         $dn->save();
                
            // }
            
        // $customer = Customer::find('ftth_id',$row[1])->first();

        // if($customer){
        //     if(!empty($row[4]))
        //     $customer->name = trim($row[4]);

        //     if(!empty($row[5]))
        //     $customer->address = trim($row[5]);

        //     if(!empty($row[6])){
        //         $township = Township::where('name','=',$row[6])->first();
        //         $customer->township_id = $township->id;
        //     }
           
        //     if(!empty($row[8]))
        //     $customer->phone_1 = trim($row[8]);

        //     if(!empty($row[9]))
        //     $customer->location = trim($row[9]);

        //     if(!empty($row[10]))
        //     $customer->installation_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row[10]));

        //     $customer->update();
           
        // }else{

        // }
        



            
        
    }
}
