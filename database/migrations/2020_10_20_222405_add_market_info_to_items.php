<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarketInfoToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dateTime('last_universalis_pull', 0)->nullable();
            $table->dateTime('last_market_update', 0)->nullable();
            $table->bigInteger('current_price')->default(0);
            $table->bigInteger('historical_price')->default(0);
            $table->smallInteger('stack_size')->default(0);
            $table->smallInteger('historical_stack_size')->default(0);
            $table->mediumInteger('sell_speed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('last_universalis_pull');
            $table->dropColumn('last_market_update');
            $table->dropColumn('current_price');
            $table->dropColumn('historical_price');
            $table->dropColumn('stack_size');
            $table->dropColumn('historical_stack_size');
            $table->dropColumn('sell_speed');
        });
    }
}
