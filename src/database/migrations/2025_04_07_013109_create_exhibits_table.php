<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExhibitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exhibits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('brand_name')->nullable();
            $table->integer('price')->unsigned();
            $table->string('explain');
            $table->foreignId('condition_id')->constrained()->cascadeOnDelete();
            $table->string('img_path');
            $table->tinyInteger('payment')->comment('1:コンビニ支払い 2:カード支払い')->nullable();
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
        Schema::dropIfExists('exhibits');
    }
}
