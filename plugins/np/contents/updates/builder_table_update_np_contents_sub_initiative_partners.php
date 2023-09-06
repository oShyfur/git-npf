<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativePartners extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_partners', function($table)
        {
            $table->string('initiative_category_id', 40);
            $table->string('initiative_sub_category_id', 40);
            $table->dropColumn('initiative_categories_id');
            $table->dropColumn('initiative_sub_categories_id');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_partners', function($table)
        {
            $table->dropColumn('initiative_category_id');
            $table->dropColumn('initiative_sub_category_id');
            $table->string('initiative_categories_id', 40);
            $table->string('initiative_sub_categories_id', 40);
        });
    }
}
