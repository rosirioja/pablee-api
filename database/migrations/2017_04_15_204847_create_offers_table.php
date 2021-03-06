<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('offers', function (Blueprint $table) {
        $table->increments('id');
        $table->string('uuid');
        $table->unsignedInteger('request_id');
        $table->unsignedInteger('trip_id');
        $table->string('currency');
        $table->float('reward', 8, 2);
        $table->datetime('delivery_date');
        $table->unsignedInteger('status_id');
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
