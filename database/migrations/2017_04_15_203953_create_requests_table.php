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
        $table->string('uuid');
        $table->increments('id');
        $table->string('title');
        $table->text('description');
        $table->string('link');
        $table->string('image_url');
        $table->unsignedInteger('quantity');
        $table->float('price', 8, 2);
        $table->float('reward', 8, 2);
        $table->float('service_fee', 8, 2);
        $table->float('total_amount', 8, 2);
        $table->string('location');
        $table->datetime('needed_at');
        $table->unsignedInteger('status_id');
        $table->unsignedInteger('created_by');
        $table->unsignedInteger('modified_by');
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
