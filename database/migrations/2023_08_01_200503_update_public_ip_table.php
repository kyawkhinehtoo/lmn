<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePublicIpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_ip_addresses', function (Blueprint $table) {
            //
            $table->string('currency',255)->after('annual_charge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_ip_addresses', function (Blueprint $table) {
            //
            $table->dropColumn('currency');
        });
    }
}
