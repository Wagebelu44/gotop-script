<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReferralAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_referral_amounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('referral_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('fund_amount')->unsigned()->comment('user add fund amount');
            $table->decimal('amount')->unsigned()->comment('referral commission');
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
        Schema::dropIfExists('user_referral_amounts');
    }
}
