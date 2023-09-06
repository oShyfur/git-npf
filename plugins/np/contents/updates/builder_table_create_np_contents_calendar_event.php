<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsCalendarEvent extends Migration
{
    public function up()
    {
        Schema::create('np_contents_calendar_event', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body');
            $table->text('location');
            $table->text('contact');
            $table->dateTime('event_date');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_calendar_event');
    }
}
