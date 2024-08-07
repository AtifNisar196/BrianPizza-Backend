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
        Schema::table('product_addons', function (Blueprint $table) {
            $table->renameColumn('product_addon_id', 'addon_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_addons', function (Blueprint $table) {
            $table->renameColumn('addon_id', 'product_addon_id');
        });
    }
};
