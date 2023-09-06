<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsTeachers extends Migration
{
    public function up()
    {
        Schema::create('np_contents_teachers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('designation');
            $table->string('field_batch', 191)->nullable();
            $table->string('id_number', 191)->nullable();
            $table->text('phone_office')->nullable();
            $table->text('phone_residence')->nullable();
            $table->text('fax')->nullable();
            $table->text('mobile')->nullable();
            $table->text('email')->nullable();
            $table->text('field_own_district')->nullable();
            $table->date('field_current_join_date')->nullable();
            $table->smallInteger('type')->nullable()->default(0);
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_teachers');
    }
}
