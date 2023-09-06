<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativeAbout extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_about', function($table)
        {
            $table->text('title');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_about', function($table)
        {
            $table->dropColumn('title');
        });
    }
}
