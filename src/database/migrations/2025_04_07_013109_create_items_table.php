<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('brand_name')->nullable();
            $table->integer('price')->unsigned();
            $table->string('explain', 255);
            $table->tinyInteger('condition')->comment('1:良好 2:目立った傷や汚れなし 3:やや傷や汚れあり 4:状態が悪い');
            $table->string('img_path');
            $table->string('status')->default('available'); // available or sold or pending
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
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('items');
    }
}
