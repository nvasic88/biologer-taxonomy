<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedListTaxonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('red_list_taxon', function (Blueprint $table) {
            $table->unsignedInteger('red_list_id');
            $table->unsignedInteger('taxon_id');
            $table->string('category', 30);

            $table->primary(['red_list_id', 'taxon_id']);

            $table->foreign('red_list_id')
                ->references('id')
                ->on('red_lists')
                ->onDelete('cascade');

            $table->foreign('taxon_id')
                ->references('id')
                ->on('taxa')
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
        Schema::dropIfExists('red_list_taxon');
    }
}
