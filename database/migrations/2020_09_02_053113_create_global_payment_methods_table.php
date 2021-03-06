<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalPaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('name')->nullable();
            $table->longText('fields')->nullable();
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
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
        Schema::dropIfExists('global_payment_methods');
    }
}
