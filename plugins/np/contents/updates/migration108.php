<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration108 extends Migration
{
    public function up()
    {
        Schema::table('system_files', function($table)
        {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('system_files', function($table)
        {
            $table->dropSoftDeletes();
        });
    }
}