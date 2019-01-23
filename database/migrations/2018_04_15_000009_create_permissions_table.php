<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Uccello\Core\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablePrefix.'permissions', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('profile_id');
            $table->unsignedInteger('capability_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('module_id')
                    ->references('id')->on($this->tablePrefix.'modules')
                    ->onDelete('cascade');

            $table->foreign('profile_id')
                    ->references('id')->on($this->tablePrefix.'profiles')
                    ->onDelete('cascade');

            $table->foreign('capability_id')
                    ->references('id')->on($this->tablePrefix.'capabilities')
                    ->onDelete('cascade');

            // Unique keys
            $table->unique([ 'module_id', 'profile_id', 'capability_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tablePrefix.'permissions');
    }
}
