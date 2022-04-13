<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryConservationLegislationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_conservation_legislation', function (Blueprint $table) {
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('leg_id');
            $table->unsignedInteger('ref_id');

            $table->primary(['country_id', 'leg_id']);

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');

            $table->foreign('leg_id')
                ->references('id')
                ->on('conservation_legislations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_conservation_legislation');
    }
}
