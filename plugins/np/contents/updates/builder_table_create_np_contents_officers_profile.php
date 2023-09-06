<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsOfficersProfile extends Migration
{
    public function up()
    {
        Schema::create('np_contents_officers_profile', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->smallInteger('sort_order');
            $table->string('id_number')->nullable();
            $table->text('name');
            $table->integer('designation');
            $table->text('phone_office');
            $table->text('phone_residence');
            $table->text('fax');
            $table->text('email');
            $table->text('mobile');
            $table->integer('officer_category');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_officers_profile');
    }
}
