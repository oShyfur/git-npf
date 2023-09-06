<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsTouristSpot extends Migration
{
    public function up()
    {
        Schema::create('np_contents_tourist_spot', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->longText('body')->nullable();
            $table->text('field_contact_person')->nullable();
            $table->text('field_tourist_spot_transportation')->nullable();
            $table->text('field_tourist_spot_location_new')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_tourist_spot');
    }
}
