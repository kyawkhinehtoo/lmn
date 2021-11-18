<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class Customers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('ftth_id');
            $table->string('name');
            $table->string('nrc')->nullable();
            $table->string('phone_1');
            $table->string('phone_2')->nullable();
            $table->longText('address');
            $table->string('location');
            $table->date('order_date');
            $table->date('installation_date')->nullable();
            $table->date('prefer_install_date')->nullable();
            $table->string('sale_channel')->nullable();
            $table->longText('sale_remark')->nullable();
            $table->foreignId('township_id');
            $table->foreignId('package_id');
            $table->foreignId('sale_person_id')->nullable();
            $table->foreignId('status_id');
            $table->foreignId('subcom_id')->nullable();;

            $table->foreignId('sn_id')->nullable();;

            $table->string('splitter_no')->nullable();
            $table->string('fiber_distance')->nullable();
            $table->longText('installation_remark')->nullable();
            
            $table->integer('fc_used')->nullable();
            $table->integer('fc_damaged')->nullable();

            $table->string('onu_serial')->nullable();
            $table->string('onu_power')->nullable();
            $table->string('contract_term')->nullable();
            $table->integer('foc')->nullable();
            $table->integer('foc_period')->nullable();
            $table->string('advance_payment')->nullable();
            $table->string('advance_payment_day')->nullable();
            $table->string('extra_bandwidth')->nullable();
            $table->string('pppoe_account')->nullable();
            $table->string('pppoe_password')->nullable();
            $table->string('currency')->nullable();
            $table->integer('deleted')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
