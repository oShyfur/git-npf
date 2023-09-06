<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsOfficersProfile3 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_officers_profile', function($table)
        {
            $table->integer('designation')->nullable()->default(null)->change();
            $table->integer('officer_category')->nullable()->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_officers_profile', function($table)
        {
            $table->integer('designation')->nullable(false)->default(0)->change();
            $table->integer('officer_category')->nullable(false)->default(0)->change();
        });
    }
}
