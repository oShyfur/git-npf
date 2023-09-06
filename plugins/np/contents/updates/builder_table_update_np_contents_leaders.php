<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsLeaders extends Migration
{
    public function up()
    {
        Schema::table('np_contents_leaders', function($table)
        {
            $table->text('field_word')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_leaders', function($table)
        {
            $table->integer('field_word')->nullable(false)->unsigned(false)->default(0)->change();
        });
    }
}
