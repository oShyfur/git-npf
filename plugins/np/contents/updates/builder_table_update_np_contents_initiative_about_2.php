<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsInitiativeAbout2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_initiative_about', function($table)
        {
            $table->renameColumn('initiative_categories_id', 'initiative_category_id');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_initiative_about', function($table)
        {
            $table->renameColumn('initiative_category_id', 'initiative_categories_id');
        });
    }
}
