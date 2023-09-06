<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureMinistries extends Migration
{
    public function up()
    {
        Schema::create('np_structure_ministries', function ($table) {
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->integer('office_type');
            $table->text('name')->nullable();
            $table->string('name_short', 255)->nullable();
            $table->string('reference_code', 100)->nullable();
            $table->smallInteger('sort_order')->default(1);
            $table->boolean('status')->default(1);
            $table->primary(['id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_ministries');
    }
}
