<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'shares',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('directory_id');
                $table->integer('type');
                $table->string('name');
                $table->string('hash', 64);
                $table->timestamps();

                $table->foreign('directory_id')->references('id')->on('directories')
                    ->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shares');
    }
}
