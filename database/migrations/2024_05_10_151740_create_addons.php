<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->longText('description');
            $table->unsignedBigInteger('addon_type_id'); // Use unsignedBigInteger for foreign key
            $table->tinyInteger('status')->default(1)->comment('0=Inactive, 1=Active');
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('addon_type_id')->references('id')->on('addon_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addons');
    }
};
