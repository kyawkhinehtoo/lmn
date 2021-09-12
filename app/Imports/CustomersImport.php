<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;

class CustomersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'No.'){
            
            $type = "ftth";
            if($row['2'] == "DIA"){
                $type="dia";
            }else if($row['2'] == "B2B"){
                $type="sme";
            }
            $phone = '';
            $phone_1=$row[13];
            $phone_2='';
            if(trim($row[13]) != null){
              $phone = explode(',',$row[13]);
            }
            if(isset($phone[0])){
                $phone_1 = $phone[0];
            }
            if(isset($phone[1])){
                $phone_2 = $phone[1];
            }
            // $township = Township::where('name','=',$row[9])->first();
            $package = Package::where('speed','=',$row[3])
                        ->where('type','=',$type)
                        ->first();
            // $project = Project::where('name','LIKE','%'.$row[11].'%')->first();
            // $subcom = User::where('name','LIKE','%'.$row[12].'%')->first();
            // $sale_person = User::where('name','LIKE','%'.$row[13].'%')->first();  
            // $status = Status::where('name','LIKE','%'.$row[23].'%')->first();  
         
        return new Customer([
            'ftth_id'=> trim($row[1]),
            'name'=> (trim($row[9]) != '')?trim($row[9]):'Unknown',              
            'phone_1'=> (trim($phone_1) != '')?trim($phone_1):'',                   
            'phone_2'=> (trim($phone_2))?trim($phone_2):'',                   
            'address'=> (trim($row[10]) != '')?trim($row[10]):'Unknown',               
            'location'=> (trim($row[14]) != '')?trim($row[14]):'',
            'township_id'=> 1,
            'package_id'=> ($package)?$package->id:20,
            'installation_date'=> (trim($row[20]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[20]):null,
            'status_id'=> 2,   
            'onu_serial' => (trim($row[21]) != '')?trim($row[21]):'',
            'fc_damaged' => (trim($row[22]) != '')?trim($row[22]):0,
            'fc_used' => (trim($row[23]) != '')?trim($row[23]):0,
            'extra_bandwidth' => (trim($row[4]) != '')?trim($row[4]):null,
            'pppoe_account' => (trim($row[7]) != '')?trim($row[7]):null,
            'pppoe_password' => (trim($row[8]) != '')?trim($row[8]):null,
            'deleted' => 0,

        ]);
            
        }
    }
}
