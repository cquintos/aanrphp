<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIspIdForeignKeyFromCommodities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->dropForeign('commodities_isp_id_foreign');
            $table->dropColumn('isp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->unsignedBigInteger('isp_id')->index()->nullable();
            $table->foreign('isp_id')->references('id')->on('isp')->onDelete('cascade');;
        });
    }
}
