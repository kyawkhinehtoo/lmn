<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pop_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pop_id');
            $table->string('device_name',255);
            $table->integer('qty')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->foreign('pop_id')->references('id')->on('pops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pop_devices', function (Blueprint $table) {
            //
        });
    }
}
