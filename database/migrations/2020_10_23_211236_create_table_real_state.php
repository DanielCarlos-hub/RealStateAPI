<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTableRealState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_state', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');

            $table->string('slug')->unique();
            $table->string('title');
            $table->string('description');
            $table->text('content');
            $table->double('price', 10, 2);
            $table->integer('bathrooms');
            $table->integer('bedrooms');
            $table->integer('garages');
            $table->integer('property_area');
            $table->integer('total_property_area');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        DB::statement('ALTER TABLE real_state MODIFY COLUMN code INT ZEROFILL NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('real_state');
    }
}
