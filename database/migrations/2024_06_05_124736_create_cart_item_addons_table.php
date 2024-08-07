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
        Schema::create('cart_item_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_item_id')->nullable();
            $table->unsignedBigInteger('product_addon_id')->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('cart_item_id')->references('id')->on('cart_items')->onDelete('cascade');
            $table->foreign('product_addon_id')->references('id')->on('product_addons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_item_addons');
    }
};
