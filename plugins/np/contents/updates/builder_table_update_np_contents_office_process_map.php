<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsOfficeProcessMap extends Migration
{
    public function up()
    {
        Schema::table('np_contents_office_process_map', function($table)
        {
            $table->string('divisionoffice', 10)->nullable()->unsigned(false)->default(null)->change();
            $table->string('upazilla_offices', 10)->nullable()->unsigned(false)->default(null)->change();
            $table->string('offices_union', 10)->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_office_process_map', function($table)
        {
            $table->integer('divisionoffice')->nullable()->unsigned(false)->default(null)->change();
            $table->integer('upazilla_offices')->nullable()->unsigned(false)->default(null)->change();
            $table->integer('offices_union')->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
