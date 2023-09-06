<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureSites4 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->integer('directorate_id')->default(0)->change();
        });
    }
}
