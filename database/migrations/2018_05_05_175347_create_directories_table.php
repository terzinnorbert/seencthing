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
                $table->unsignedInteger('folder_id');
                $table->integer('type');
                $table->string('name');
                $table->string('path')->default('/');
                $table->unsignedInteger('size')->default(0);
                $table->datetime('modification_time');
                $table->datetime('sync_time');
                $table->integer('state')->default(\App\Directory::STATE_AVAILABLE);
                $table->datetime('expiration_time')->nullable();
                $table->timestamps();

                $table->foreign('folder_id')->references('id')->on('folders')
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
        Schema::dropIfExists('directories');
    }
}