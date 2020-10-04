<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsfeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsfeeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->string('title');
            $table->string('image')->nullable();
            $table->longText('content')->nullable();
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
            $table->enum('important_news', ['Yes', 'No'])->default('No')->nullable();
            $table->enum('service_update', ['Yes', 'No'])->default('No')->nullable();
            $table->enum('news_feed', ['Yes', 'No'])->default('Yes')->nullable();
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
        Schema::dropIfExists('newsfeeds');
    }
}
