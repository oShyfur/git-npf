<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureSiteTag extends Migration
{
    public function up()
    {
        Schema::create('np_structure_site_tag', function ($table) {

            $table->engine = 'InnoDB';
            $table->integer('site_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->primary(['site_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_site_tag');
    }
}
