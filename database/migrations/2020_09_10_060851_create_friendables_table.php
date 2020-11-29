<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friendables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('friend_id');
            $table->unsignedBigInteger('friendable_id');
            $table->string('friendable_type');
            $table->dateTime('chated_at');
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
        Schema::dropIfExists('friendables');
    }
}
