<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsPhotogallery extends Migration
{
    public function up()
    {
        Schema::table('np_contents_photogallery', function($table)
        {
            $table->boolean('is_pin')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_photogallery', function($table)
        {
            $table->dropColumn('is_pin');
        });
    }
}
