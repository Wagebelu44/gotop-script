<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('drip_feed_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('provider_order_id', 200)->nullable();
            $table->decimal('charges', 15, 5)->nullable();
            $table->decimal('original_charges', 15, 5)->default('0.00000');
            $table->decimal('unit_price', 15, 5)->default('0.00000');
            $table->decimal('original_unit_price', 15, 5)->default('0.00000');
            $table->text('link')->nullable();
            $table->unsignedInteger('start_counter')->nullable();
            $table->unsignedInteger('remains')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->longText('auto_order_response')->nullable();
            $table->text('custom_comments')->nullable();
            $table->string('mode')->nullable();
            $table->enum('source', ['web', 'api'])->nullable();
            $table->dateTime('order_viewable_time')->nullable();
            $table->longText('text_area_1')->nullable();
            $table->longText('text_area_2')->nullable();
            $table->longText('additional_inputs')->nullable();
            $table->string('refill_status')->default(0);
            $table->enum('refill_order_status', ['success', 'processing', 'pending', 'rejected', 'cancelled', 'error'])->nullable();
            $table->enum('status', ['awaiting', 'processing', 'inprogress', 'completed', 'pending', 'partial', 'cancelled', 'failed', 'error'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
}
