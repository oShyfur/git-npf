<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDistrictBranding extends Migration
{
    public function up()
    {
        Schema::create('np_contents_district_branding', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->longText('body')->nullable();
            $table->text('slogan')->nullable();
            $table->text('video_link')->nullable();
            $table->longText('details_plan')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_district_branding');
    }
}
