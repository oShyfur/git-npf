<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsOfficersProfile4 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_officers_profile', function($table)
        {
            $table->text('designation')->nullable()->unsigned(false)->default(null)->change();
            $table->text('officer_category')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_officers_profile', function($table)
        {
            $table->integer('designation')->nullable()->unsigned(false)->default(null)->change();
            $table->integer('officer_category')->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
