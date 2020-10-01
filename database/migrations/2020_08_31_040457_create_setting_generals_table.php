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
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('panel_name')->nullable();
            $table->string('timezone')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_sign')->nullable();
            $table->string('currency_name')->nullable();
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
            $table->enum('newsfeed_align', ['Left', 'Right'])->default('Right');
            $table->enum('newsfeed', ['Yes', 'No'])->default('Yes');
            $table->enum('horizontal_menu', ['Yes', 'No'])->default('Yes');
            $table->enum('total_order', ['Yes', 'No'])->default('No');
            $table->enum('total_spent', ['Yes', 'No'])->default('No');
            $table->enum('point', ['Yes', 'No'])->default('No');
            $table->enum('account_status', ['Yes', 'No'])->default('No');
            $table->enum('redeem', ['Yes', 'No'])->default('No');
            $table->enum('panel_type', ['Main', 'Child'])->default('Main');
            $table->unsignedBigInteger('main_panel_id')->nullable()->comment('Main panel ID for Child Panel');
            $table->string('main_panel_domain')->nullable()->comment('Main panel Domain for Child Panel');
            $table->enum('status', ['Active', 'Canceled'])->default('Active');
            $table->unsignedBigInteger('updated_by');
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
