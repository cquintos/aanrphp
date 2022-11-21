<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommodityIdForeignToCommoditySubtypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodity_subtypes', function (Blueprint $table) {
            $table->unsignedBigInteger('commodity_id')->index()->nullable();
            $table->foreign('commodity_id')->references('id')->on('commodities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commodity_subtypes', function (Blueprint $table) {
            $table->dropForeign('commodity_subtypes_commodity_id_foreign');
            $table->dropColumn('commodity_id');
        });
    }
}
