<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComboPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combo_properties', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('combo_id')->unsigned();
            $table->bigInteger('property_id')->unsigned();
            $table->foreign('combo_id')->references('id')->on('combos');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->string("value");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('combo_properties');
    }
}
