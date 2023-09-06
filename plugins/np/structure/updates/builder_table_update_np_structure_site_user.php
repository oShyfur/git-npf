<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureSiteUser extends Migration
{
    public function up()
    {
        Schema::table('np_structure_site_user', function($table)
        {
            $table->primary(['site_id','user_id']);
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_site_user', function($table)
        {
            $table->dropPrimary(['site_id','user_id']);
        });
    }
}
