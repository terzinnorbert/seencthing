<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'directories',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('parent_id');
                $table->unsignedInteger('folder_id');
                $table->integer('type');
                $table->string('name');
                $table->unsignedInteger('size')->default(0);
                $table->datetime('modification_time');
                $table->datetime('sync_time');
                $table->timestamps();

                $table->foreign('folder_id')->references('id')->on('folders');
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
        Schema::dropIfExists('directories');
    }
}
