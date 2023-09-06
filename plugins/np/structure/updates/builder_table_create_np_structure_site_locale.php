<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureSiteLocale extends Migration
{
    public function up()
    {
        Schema::create('np_structure_site_locale', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('site_id');
            $table->integer('locale_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_structure_site_locale');
    }
}
