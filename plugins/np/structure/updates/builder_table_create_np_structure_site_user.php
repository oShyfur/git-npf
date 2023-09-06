<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureSiteUser extends Migration
{
    public function up()
    {
        Schema::create('np_structure_site_user', function ($table) {

            $table->engine = 'InnoDB';
            $table->integer('site_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->boolean('default')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_site_user');
    }
}
