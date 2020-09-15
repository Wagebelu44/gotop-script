<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDripFeedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drip_feed_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('runs');
            $table->integer('interval');
            $table->integer('total_quantity');
            $table->string('total_charges');
            $table->string('status')->nullable()->comment("['CANCELLED', 'COMPLETED', 'ACTIVE']");
            $table->unsignedBigInteger('panel_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drip_feed_orders');
    }
}
