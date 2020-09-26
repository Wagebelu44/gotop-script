<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exported_orders', function (Blueprint $table) {
            $table->id();
            $table->date('from');
            $table->date('to');
            $table->string('status');
            $table->string('mode');
            $table->string('format');
            $table->string('include_columns', 500);
            $table->text('user_ids');
            $table->text('service_ids');
            $table->text('provider_ids');
            $table->unsignedBigInteger('panel_id');
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
        Schema::dropIfExists('exported_orders');
    }
}
