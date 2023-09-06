<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSubInitiativePublications2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_sub_initiative_publications', function($table)
        {
            $table->string('initiative_category_id', 40)->nullable()->change();
            $table->string('initiative_sub_category_id', 40)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_sub_initiative_publications', function($table)
        {
            $table->string('initiative_category_id', 40)->nullable(false)->change();
            $table->string('initiative_sub_category_id', 40)->nullable(false)->change();
        });
    }
}
