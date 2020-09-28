<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exported_payments', function (Blueprint $table) {
            $table->id();
            $table->date('from');
            $table->date('to');
            $table->string('status');
            $table->string('mode');
            $table->string('format');
            $table->string('include_columns', 255);
            $table->text('user_ids');
            $table->unsignedBigInteger('panel_id');
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
        Schema::dropIfExists('exported_payments');
    }
}
