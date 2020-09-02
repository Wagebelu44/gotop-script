<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_generals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('updated_by');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('timezone')->nullable();
            $table->string('currency_format')->nullable();
            $table->string('rates_rounding')->nullable();
            $table->unsignedTinyInteger('ticket_system')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->string('tickets_per_user')->nullable();
            $table->unsignedTinyInteger('signup_page')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->unsignedTinyInteger('email_confirmation')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->unsignedTinyInteger('skype_field')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->unsignedTinyInteger('name_fields')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->unsignedTinyInteger('terms_checkbox')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->unsignedTinyInteger('reset_password')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->unsignedTinyInteger('average_time')->nullable()->comment('0 => Disabled, 1 => Enabled');
            $table->string('drip_feed_interval')->nullable();
            $table->longText('custom_header_code')->nullable();
            $table->longText('custom_footer_code')->nullable();
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
        Schema::dropIfExists('setting_generals');
    }
}
