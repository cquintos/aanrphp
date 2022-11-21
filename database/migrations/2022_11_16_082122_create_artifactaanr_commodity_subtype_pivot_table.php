<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArtifactaanrCommoditySubtypePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artifactaanr_commodity_subtype', function (Blueprint $table) {
            $table->unsignedBigInteger('artifactaanr_id')->index();
            $table->foreign('artifactaanr_id')->references('id')->on('artifactaanr')->onDelete('cascade');
            $table->unsignedBigInteger('commodity_subtype_id')->index();
            $table->foreign('commodity_subtype_id')->references('id')->on('commodity_subtypes')->onDelete('cascade');
            $table->primary(['artifactaanr_id', 'commodity_subtype_id'], 'artifact_commodity_subtype');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artifactaanr_commodity_subtype');
    }
}
