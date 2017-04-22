<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('requests', function (Blueprint $table) {
        $table->increments('id');
        $table->string('uuid');
        $table->string('title');
        $table->text('description');
        $table->string('link');
        $table->string('image_url');
        $table->unsignedInteger('quantity');
        $table->string('currency');
        $table->float('price', 8, 2);
        $table->float('reward', 8, 2);
        $table->float('service_fee', 8, 2);
        $table->float('total_amount', 8, 2);
        $table->string('deliver_from');
        $table->string('deliver_to');
        $table->datetime('needed_at');
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
      Schema::dropIfExists('requests');
    }
}
