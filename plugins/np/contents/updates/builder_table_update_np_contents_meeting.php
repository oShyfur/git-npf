<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsMeeting extends Migration
{
    public function up()
    {
        Schema::table('np_contents_meeting', function($table)
        {
            $table->dropColumn('section_dcoffice');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_meeting', function($table)
        {
            $table->string('section_dcoffice', 10)->nullable();
        });
    }
}
