<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->unsignedBigInteger('panel_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique()->nullable();
            $table->string('skype_name')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('api_key')->unique()->nullable();
            $table->string('referral_key')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedTinyInteger('email_confirmation_status')->nullable()->comment('1 => Enabled');
            $table->string('password');
            $table->rememberToken();
            $table->enum('affiliate_status', ['Active', 'Deactivated'])->default('Active');
            $table->enum('status', ['Pending', 'Active', 'Deactivated'])->default('Pending');
            $table->string('timezone', 200)->nullable();
            $table->timestamp('last_login_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
