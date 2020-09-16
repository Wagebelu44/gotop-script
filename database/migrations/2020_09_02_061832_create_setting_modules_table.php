<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('commission_rate', 10, 2)->default(0);
            $table->enum('approve_payout', ['manual', 'auto'])->default('manual');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['affiliate', 'child_panels', 'free_balance'])->nullable();
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
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
        Schema::dropIfExists('setting_modules');
    }
}
