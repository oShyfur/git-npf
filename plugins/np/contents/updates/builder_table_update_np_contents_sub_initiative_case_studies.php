<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativeCaseStudies extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_case_studies', function($table)
        {
            $table->boolean('is_pin')->default(0);
            $table->boolean('is_popular')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_case_studies', function($table)
        {
            $table->dropColumn('is_pin');
            $table->dropColumn('is_popular');
        });
    }
}
