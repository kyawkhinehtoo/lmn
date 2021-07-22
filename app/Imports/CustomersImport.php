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
        if($row[0] != 'ID'){
        
            $township = Township::where('name','=',$row[9])->first();
            $package = Package::where('name','LIKE','%'.$row[10].'%')->first();
            $project = Project::where('name','LIKE','%'.$row[11].'%')->first();
            $subcom = User::where('name','LIKE','%'.$row[12].'%')->first();
            $sale_person = User::where('name','LIKE','%'.$row[13].'%')->first();  
            $status = Status::where('name','LIKE','%'.$row[23].'%')->first();  
         
        return new Customer([
            'ftth_id'=> $row[0],
            'name'=> $row[1],
            'nrc'=> $row[2],
            'dob'=> $row[3],                
            'phone_1'=> $row[4],               
            'phone_2'=> $row[5],               
            'email'=> $row[6],                 
            'address'=> $row[7],               
            'location'=> $row[8],
            'township_id'=> $township->id,
            'package_id'=> $package->id,
            'project_id'=> $project->id,
            'subcom_id'=> $subcom->id,
            'sale_person_id'=> $sale_person->id,               
            'order_date'=> $row[14],            
            'installation_date'=> $row[15],
            'deposit_status'=> $row[16],      
            'deposit_receive_date'=> $row[17],  
            'deposit_receive_from'=> $row[18],   
            'deposit_receive_amount'=> $row[19], 
            'order_form_sign_status'=> $row[20],
            'bill_start_date'=> $row[21],      
            'sale_channel'=> $row[22],  
            'status_id'=> $status->id,       
            'remark'=> $row[24],
        ]);
            
        }
    }
}
