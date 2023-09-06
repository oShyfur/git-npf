<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsDivcomMeeting extends Migration
{
    public function up()
    {
        Schema::table('np_contents_divcom_meeting', function($table)
        {
            $table->dropColumn('section_divcom');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_divcom_meeting', function($table)
        {
            $table->string('section_divcom', 10)->nullable();
        });
    }
}
