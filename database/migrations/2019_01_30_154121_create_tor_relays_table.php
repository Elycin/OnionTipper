<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTorRelaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tor_relays', function (Blueprint $table) {
            $table->increments('id');
            $table->string("nickname");
            $table->string("fingerprint")->unique();
            $table->string("email")->nullable();
            $table->string("donation_address")->nullable();
            $table->dateTime("first_seen");
            $table->integer("consensus_weight")->default(0);
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
        Schema::dropIfExists('tor_relays');
    }
}
