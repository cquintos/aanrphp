<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndustryIdToArtifactaanrCommodity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('artifactaanr_commodity', function (Blueprint $table) {
            //
            $table->bigInteger('industry_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('artifactaanr_commodity', function (Blueprint $table) {
            //
            $table->dropColumn('industry_id');
        });
    }
}
