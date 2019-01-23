<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Uccello\Core\Database\Migrations\Migration;

class CreateDomainsModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablePrefix.'domains_modules', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('domain_id');
            $table->unsignedInteger('module_id');

            // Foreign keys
            $table->foreign('domain_id')
                    ->references('id')->on($this->tablePrefix.'domains')
                    ->onDelete('cascade');

            $table->foreign('module_id')
                    ->references('id')->on($this->tablePrefix.'modules')
                    ->onDelete('cascade');

            // Unique keys
            $table->unique([ 'domain_id', 'module_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tablePrefix.'domains_modules');
    }
}
