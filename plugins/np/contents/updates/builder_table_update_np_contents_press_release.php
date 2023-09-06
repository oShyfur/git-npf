<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsPressRelease extends Migration
{
    public function up()
    {
        Schema::table('np_contents_press_release', function($table)
        {
            $table->text('youtube_link')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_press_release', function($table)
        {
            $table->dropColumn('youtube_link');
        });
    }
}
