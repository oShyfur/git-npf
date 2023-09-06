<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsServiceBox extends Migration
{
    public function up()
    {
        Schema::create('np_contents_service_box', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->integer('sort_order')->default(0);
            $table->text('link1')->nullable();
            $table->text('link2')->nullable();
            $table->text('link3')->nullable();
            $table->text('link4')->nullable();
            $table->text('title_color')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_service_box');
    }
}
