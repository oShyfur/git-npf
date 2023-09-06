<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsTeamMembers2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->integer('sort_order')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
