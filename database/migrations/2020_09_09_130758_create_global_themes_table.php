<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('snapshot')->nullable();
            $table->enum('status', ['Active', 'Deactivated'])->default('Deactivated');
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
        Schema::dropIfExists('global_themes');
    }
}
