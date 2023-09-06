<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsOfficerList extends Migration
{
    public function up()
    {
        Schema::create('np_contents_officer_list', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('designation');
            $table->string('field_batch')->nullable()->default(null);
            $table->string('id_number')->nullable()->default(null);
            $table->text('phone_office')->nullable();
            $table->text('phone_residence')->nullable();
            $table->text('fax')->nullable();
            $table->date('field_current_join_date')->nullable();
            $table->text('field_own_district')->nullable();
            $table->text('mobile')->nullable();
            $table->text('email')->nullable();
            $table->smallInteger('section')->nullable()->default(0);
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_officer_list');
    }
}
