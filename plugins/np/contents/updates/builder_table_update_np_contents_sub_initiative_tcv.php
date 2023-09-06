<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativeTcv extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_tcv', function($table)
        {
            $table->text('title');
            $table->text('value');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_tcv', function($table)
        {
            $table->dropColumn('title');
            $table->dropColumn('value');
        });
    }
}
