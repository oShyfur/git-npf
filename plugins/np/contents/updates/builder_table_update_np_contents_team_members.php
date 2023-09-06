<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsTeamMembers extends Migration
{
    public function up()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->text('body')->nullable()->change();
            $table->text('email')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->text('skype')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_team_members', function($table)
        {
            $table->text('body')->nullable(false)->change();
            $table->text('email')->nullable(false)->change();
            $table->text('phone')->nullable(false)->change();
            $table->text('skype')->nullable(false)->change();
        });
    }
}
