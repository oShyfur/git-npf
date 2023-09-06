<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsOfficersProfile extends Migration
{
    public function up()
    {
        Schema::table('np_contents_officers_profile', function($table)
        {
            $table->integer('officer_category')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_officers_profile', function($table)
        {
            $table->integer('officer_category')->nullable(false)->change();
        });
    }
}
