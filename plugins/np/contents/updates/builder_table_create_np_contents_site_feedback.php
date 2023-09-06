<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsSiteFeedback extends Migration
{
    public function up()
    {
        Schema::create('np_contents_site_feedback', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->smallInteger('form_id')->nullable();
            $table->integer('site_id');
            $table->text('data')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_site_feedback');
    }
}