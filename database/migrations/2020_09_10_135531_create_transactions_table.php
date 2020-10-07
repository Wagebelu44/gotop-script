<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('reseller_payment_methods_setting_id')->nullable();
            $table->string('tnx_id', 255)->nullable();
            $table->enum('transaction_type', ['withdraw', 'deposit']);
            $table->string('transaction_flag', 200)->comment('payment_gateway, refund, admin_panel, order_place, free_balance, bonus_deposit, drip_feed_cancel, redeem, child_panel, affiliate, other');
            $table->double('amount', 10, 2)->nullable();
            $table->string('memo', 250)->nullable();
            $table->string('fraud_risk', 250)->nullable();
            $table->longText('transaction_detail')->nullable();
            $table->longText('payment_gateway_response')->nullable();
            $table->unsignedInteger('sequence_number')->nullable();
            $table->enum('status', ['hold', 'done']);
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
        Schema::dropIfExists('transactions');
    }
}
