<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsLeaders extends Migration
{
    public function up()
    {
        Schema::create('np_contents_leaders', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->smallInteger('designation');
            $table->text('field_mobile')->nullable();
            $table->text('field_email')->nullable();
            $table->text('field_phone_residence')->nullable();
            $table->text('field_phone_office')->nullable();
            $table->text('field_fax_office')->nullable();
            $table->text('field_fax_residence')->nullable();
            $table->smallInteger('field_highest_educational_degree')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_leaders');
    }
}
