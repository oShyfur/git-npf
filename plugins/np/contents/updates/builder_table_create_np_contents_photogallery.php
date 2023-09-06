<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsPhotogallery extends Migration
{
    public function up()
    {
        Schema::create('np_contents_photogallery', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->integer('type')->nullable()->default(0);
            $table->dateTime('taken_date')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_photogallery');
    }
}
