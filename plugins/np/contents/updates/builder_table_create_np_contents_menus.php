<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsMenus extends Migration
{
    public function up()
    {
        Schema::create('np_contents_menus', function ($table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->string('id', 40)->primary();
            $table->tinyInteger('depth', false, true);
            $table->text('title')->charset('utf8mb4')->collate('utf8mb4_unicode_ci')->nullable();
            $table->string('link_type');
            $table->text('link_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('parent_id')->nullable();
            $table->integer('site_id')->unsigned()->index();
            $table->boolean('status')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable()->default(1);
            $table->integer('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_menus');
    }
}
