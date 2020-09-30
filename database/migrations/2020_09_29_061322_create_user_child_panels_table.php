<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserChildPanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_child_panels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('user_id');
            $table->string('domain')->nullable();
            $table->string('currency')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('status', ['Pending', 'Active', 'Frozen', 'Terminated', 'Canceled'])->default('Pending');
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('invoice_sent_at')->nullable();
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
        Schema::dropIfExists('user_child_panels');
    }
}
