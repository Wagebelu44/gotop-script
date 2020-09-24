<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedeemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('user_id');
            $table->string('account_status')->nullable();
            $table->string('redeem_point')->nullable();
            $table->decimal('spent_amount', 15, 5)->default('0.00000');
            $table->decimal('redeem_amount', 15, 5)->default('0.00000');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('redeems');
    }
}
