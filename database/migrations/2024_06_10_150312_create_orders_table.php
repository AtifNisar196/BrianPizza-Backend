<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('suburb')->nullable();
            $table->string('address')->nullable();
            $table->smallInteger('order_type')->comment('1=Delivery, 2=Pickup')->nullable();
            $table->string('timings')->nullable();
            $table->string('payment_method')->nullable();
            $table->longText('comments')->nullable();
            $table->float('sub_total', 10, 2)->nullable();
            $table->float('tax', 10, 2)->nullable();
            $table->float('total', 10, 2)->nullable();
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
        Schema::dropIfExists('orders');
    }
};
