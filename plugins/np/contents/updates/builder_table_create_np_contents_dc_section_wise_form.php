<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDcSectionWiseForm extends Migration
{
    public function up()
    {
        Schema::create('np_contents_dc_section_wise_form', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->longText('body')->nullable();
            $table->tinyInteger('section_id')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_dc_section_wise_form');
    }
}
