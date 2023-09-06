<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsPhysicalLocations extends Migration
{
    public function up()
    {
        Schema::table('np_contents_physical_locations', function($table)
        {
            $table->text('address');
            $table->string('latitude', 100);
            $table->string('longitude', 100);
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_physical_locations', function($table)
        {
            $table->dropColumn('address');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
