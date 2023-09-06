<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsStaffList extends Migration
{
    public function up()
    {
        Schema::create('np_contents_staff_list', function ($table) {

            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('designation');
            $table->text('mobile')->nullable();
            $table->text('email')->nullable();
            $table->text('phone_office')->nullable();
            $table->text('phone_residence')->nullable();
            $table->date('field_date_of_join')->nullable();
            $table->text('field_own_dist_new')->nullable();
            $table->smallInteger('section')->nullable()->default(0);
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_staff_list');
    }
}
