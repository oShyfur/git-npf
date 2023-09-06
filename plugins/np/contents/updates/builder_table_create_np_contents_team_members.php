<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsTeamMembers extends Migration
{
    public function up()
    {
        Schema::create('np_contents_team_members', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('name');
            $table->text('email');
            $table->text('skype');
            $table->text('body');
            $table->text('phone');
            $table->text('team_designation');
            $table->string('team_cluster_id', 40);
            $table->string('team_name_id', 40);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_team_members');
    }
}
