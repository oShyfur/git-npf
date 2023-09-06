<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsMenus extends Migration
{
    public function up()
    {
        Schema::table('np_contents_menus', function($table)
        {
            $table->boolean('default_text_color')->default(1);
            $table->boolean('default_background_color')->default(1);
            $table->integer('depth')->nullable(false)->unsigned()->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_menus', function($table)
        {
            $table->dropColumn('default_text_color');
            $table->dropColumn('default_background_color');
            $table->boolean('depth')->nullable(false)->unsigned()->default(null)->change();
        });
    }
}
