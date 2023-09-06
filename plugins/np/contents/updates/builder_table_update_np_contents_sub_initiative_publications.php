<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativePublications extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_publications', function($table)
        {
            $table->text('body');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_publications', function($table)
        {
            $table->dropColumn('body');
        });
    }
}
