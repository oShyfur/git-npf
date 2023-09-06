<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsMeeting extends Migration
{
    public function up()
    {
        Schema::create('np_contents_meeting', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->text('field_dcmeeting_member')->nullable();
            $table->text('field_meeting_notice')->nullable();
            $table->string('section_dcoffice', 10)->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_meeting');
    }
}
