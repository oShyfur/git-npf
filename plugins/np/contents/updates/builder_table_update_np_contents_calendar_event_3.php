<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsCalendarEvent3 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_calendar_event', function($table)
        {
            $table->dateTime('end_date');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_calendar_event', function($table)
        {
            $table->dropColumn('end_date');
        });
    }
}
