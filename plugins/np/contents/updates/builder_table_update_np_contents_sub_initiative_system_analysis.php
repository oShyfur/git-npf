<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativeSystemAnalysis extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_system_analysis', function($table)
        {
            $table->text('title');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_system_analysis', function($table)
        {
            $table->dropColumn('title');
        });
    }
}
