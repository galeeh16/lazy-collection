<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enterprise', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('industry_aggregation');
            $table->string('industry_code');
            $table->string('industry_name');
            $table->string('units');
            $table->string('variable_code');
            $table->string('variable_name');
            $table->string('variable_category');
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise');
    }
};
