<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('send_by');
            $table->string('sender_role')->nullable();
            $table->string('subject');
            $table->string('subject_ids')->nullable();
            $table->string('payment_type')->nullable();
            $table->longText('description')->nullable();
            $table->enum('status', ['pending', 'answered', 'closed'])->default('pending');
            $table->unsignedTinyInteger('seen_by_admin')->default(0);
            $table->unsignedTinyInteger('seen_by_user')->default(0);

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
        Schema::dropIfExists('tickets');
    }
}
