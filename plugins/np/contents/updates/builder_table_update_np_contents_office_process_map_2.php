<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsOfficeProcessMap2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_office_process_map', function($table)
        {
            $table->dropColumn('divisionoffice');
            $table->dropColumn('upazilla_offices');
            $table->dropColumn('offices_union');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_office_process_map', function($table)
        {
            $table->string('divisionoffice', 10)->nullable();
            $table->string('upazilla_offices', 10)->nullable();
            $table->string('offices_union', 10)->nullable();
        });
    }
}
