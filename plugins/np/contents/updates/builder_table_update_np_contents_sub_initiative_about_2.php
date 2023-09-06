<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativeAbout2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_about', function($table)
        {
            $table->text('body')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_about', function($table)
        {
            $table->string('body', 191)->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
