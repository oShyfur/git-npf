<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsInitiativeCategories extends Migration
{
    public function up()
    {
        Schema::table('np_contents_initiative_categories', function($table)
        {
            $table->integer('sort_order')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_initiative_categories', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
