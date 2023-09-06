<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsTeamMembers3 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->string('team_cluster_id', 40)->nullable()->change();
            $table->text('team_designation')->nullable()->change();
            $table->string('team_name_id', 40)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->string('team_cluster_id', 40)->nullable(false)->change();
            $table->text('team_designation')->nullable(false)->change();
            $table->string('team_name_id', 40)->nullable(false)->change();
        });
    }
}
