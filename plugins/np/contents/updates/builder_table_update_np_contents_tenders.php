<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsTenders extends Migration
{
    public function up()
    {
        Schema::table('np_contents_tenders', function($table)
        {
            $table->text('tender_type');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_tenders', function($table)
        {
            $table->dropColumn('tender_type');
        });
    }
}
