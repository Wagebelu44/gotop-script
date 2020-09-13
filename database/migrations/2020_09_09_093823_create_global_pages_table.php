<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->string('meta_description')->nullable();
            $table->enum('is_public', ['Yes', 'No'])->default('Yes');
            $table->enum('is_editable', ['Yes', 'No'])->default('Yes');
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
        Schema::dropIfExists('global_pages');
    }
}
