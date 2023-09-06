<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsCalendarEvent2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_calendar_event', function($table)
        {
            $table->text('email')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_calendar_event', function($table)
        {
            $table->text('email')->nullable(false)->change();
        });
    }
}
