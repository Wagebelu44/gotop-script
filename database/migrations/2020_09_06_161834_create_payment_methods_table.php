<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('global_payment_method_id');
            $table->string('method_name');
            $table->decimal('minimum', 8, 2)->default(0.00);
            $table->decimal('maximum', 8, 2)->default(0.00);
            $table->enum('new_user_status', ['active', 'inactive'])->default('inactive');
            $table->enum('visibility', ['disabled', 'enabled'])->default('disabled');
            $table->longText('details')->nullable();
            $table->unsignedInteger('sort')->default(0);
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
        Schema::dropIfExists('payment_methods');
    }
}
