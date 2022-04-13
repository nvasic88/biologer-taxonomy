<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryRedListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_red_list', function (Blueprint $table) {
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('red_list_id');
            $table->unsignedInteger('ref_id');

            $table->primary(['country_id', 'red_list_id']);

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');

            $table->foreign('red_list_id')
                ->references('id')
                ->on('red_lists')
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
        Schema::dropIfExists('country_red_list');
    }
}
