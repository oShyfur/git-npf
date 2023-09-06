<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSiteFeedback2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_site_feedback', function($table)
        {
            $table->string('form_id', 100)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_site_feedback', function($table)
        {
            $table->string('form_id', 10)->change();
        });
    }
}