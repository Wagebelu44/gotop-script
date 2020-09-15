<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_id'); 
            $table->unsignedBigInteger('category_id');
            $table->unsignedInteger('sort')->nullable();
            $table->string('name');
            $table->enum('mode', ['manual', 'auto']);
            $table->enum('drip_feed_status', ['allow', 'disallow'])->nullable();
            $table->enum('refill_status', ['allow', 'disallow'])->nullable();
            $table->enum('link_duplicates', ['allow', 'disallow'])->nullable();
            $table->enum('service_type', [
                'Default',
                'SEO',
                'SEO2',
                'Custom Comments',
                'Custom Comments Package',
                'Comment Likes',
                'Mentions',
                'Mentions with Hashtags',
                'Mentions Custom List',
                'Mentions Hashtag',
                'Mentions Users Followers',
                'Mentions Media Likers',
                'Package',
                'Poll',
                'Comment Replies',
                'Invites From Groups',
                'Subscriptions',
            ])->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('increment')->nullable();
            $table->integer('auto_overflow')->nullable();
            $table->unsignedBigInteger('min_quantity')->nullable();
            $table->unsignedBigInteger('max_quantity')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('provider_service_id')->nullable();
            $table->boolean('provider_sync_status')->default(false);
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('service_average_time', 100)->nullable();
            $table->string('subscription_type', 200)->nullable();
            $table->boolean('is_user')->default(0);
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
            $table->foreign('panel_id')->on('panel_admins')->references('id')->onDelete('cascade');
            $table->foreign('category_id')->on('service_categories')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('services');
    }
}
