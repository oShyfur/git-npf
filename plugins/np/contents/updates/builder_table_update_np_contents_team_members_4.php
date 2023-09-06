<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsTeamMembers4 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->integer('team_designation')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->text('team_designation')->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
