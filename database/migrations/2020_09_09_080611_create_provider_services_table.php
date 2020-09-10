<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_services', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id')->unsigned();
            $table->integer('panel_id')->unsigned();
            $table->integer('provider_id')->unsigned();
            $table->integer('provider_service_id')->unsigned();
            $table->string('name', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->double('rate', 15, 8)->nullable();
            $table->unsignedBigInteger('min')->nullable();
            $table->unsignedBigInteger('max')->nullable();
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
        Schema::dropIfExists('provider_services');
    }
}
