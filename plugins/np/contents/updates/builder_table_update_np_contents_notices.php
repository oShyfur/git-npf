<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsNotices extends Migration
{
    public function up()
    {
        Schema::table('np_contents_notices', function($table)
        {
            $table->boolean('publish')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_notices', function($table)
        {
            $table->boolean('publish')->nullable(false)->change();
        });
    }
}
