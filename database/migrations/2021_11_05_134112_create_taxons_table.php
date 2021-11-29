<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxa', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('rank')->index();
            $table->unsignedInteger('rank_level')->index();
            $table->string('author')->nullable();
            $table->unsignedInteger('fe_old_id')->nullable();
            $table->string('fe_id')->nullable();
            $table->boolean('restricted')->default(false);
            $table->boolean('allochthonous')->default(false);
            $table->boolean('invasive')->default(false);
            $table->boolean('uses_atlas_codes')->default(false);
            $table->string('ancestors_names', 1000)->nullable();
            $table->timestamps();

            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxa', function (Blueprint $table) {
            Schema::dropIfExists('taxa');
        });
    }
}
