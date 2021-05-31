<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string     $ftth_id
 * @property string     $name
 * @property string     $nrc
 * @property Date       $dob
 * @property string     $phone_1
 * @property string     $phone_2
 * @property string     $email
 * @property string     $address
 * @property string     $location
 * @property Date       $order_date
 * @property Date       $installation_date
 * @property Date       $deposit_receive_date
 * @property string     $deposit_status
 * @property string     $deposit_receive_from
 * @property string     $deposit_receive_amount
 * @property boolean    $order_form_sign_status
 * @property Date       $bill_start_date
 * @property string     $sale_channel
 * @property string     $remark
 * @property int        $created_at
 * @property int        $updated_at
 */
class Customer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ftth_id', 'name', 'nrc', 'dob', 'phone_1', 'phone_2', 'email', 'address', 'location', 'order_date', 'installation_date', 'deposit_receive_date', 'contract_period', 'deposit_status', 'deposit_receive_from', 'deposit_receive_amount', 'order_form_sign_status', 'bill_start_date', 'sale_channel', 'remark', 'township_id','package_id', 'sale_person_id', 'project_id', 'created_at', 'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ftth_id' => 'string', 'name' => 'string', 'nrc' => 'string', 'dob' => 'date', 'phone_1' => 'string', 'phone_2' => 'string', 'email' => 'string', 'address' => 'string', 'location' => 'string', 'order_date' => 'date', 'installation_date' => 'date', 'deposit_receive_date' => 'date', 'deposit_status' => 'string', 'deposit_receive_from' => 'string', 'deposit_receive_amount' => 'string', 'order_form_sign_status' => 'boolean', 'bill_start_date' => 'date', 'sale_channel' => 'string', 'remark' => 'string', 'created_at' => 'timestamp', 'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'dob', 'order_date', 'installation_date', 'deposit_receive_date', 'bill_start_date', 'created_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations 
    public function township()
    {
        return $this->hasOne(Township::class);
    }
    public function package()
    {
        return $this->hasOne(Package::class);
    }
    public function salePerson()
    {
        return $this->hasOne(SalePerson::class);
    }
    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
