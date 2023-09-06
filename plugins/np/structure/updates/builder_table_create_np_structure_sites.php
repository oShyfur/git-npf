<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureSites extends Migration
{
    public function up()
    {
        Schema::create('np_structure_sites', function ($table) {

            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('uuid', 40);
            $table->string('name', 255);
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('ministry_id')->unsigned();
            $table->integer('layer_id')->unsigned();
            $table->string('site_email', 80)->nullable();
            $table->string('site_default_lang', 10);
            $table->string('site_theme_code', 50);
            $table->string('slogan', 200)->nullable();
            $table->text('site_mission')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('status')->unsigned()->default(1);
            $table->dateTime('last_content_updated')->nullable();
            $table->dateTime('go_live_date')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->integer('deleted_by')->nullable()->unsigned();
            $table->string('db_id', 40);
            $table->integer('cluster_id')->unsigned();
            $table->smallInteger('level_id')->nullable()->default(0);
            $table->text('site_meta')->nullable();
            $table->integer('old_id')->nullable();
            $table->boolean('active')->default(1);
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_sites');
    }
}
