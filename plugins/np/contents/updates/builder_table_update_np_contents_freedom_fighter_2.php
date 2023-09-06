<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsFreedomFighter2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_freedom_fighter', function($table)
        {
            $table->text('gazette_number')->nullable();
            $table->text('mobile')->nullable();
            $table->string('sector')->nullable();
            $table->text('union')->nullable();
            $table->text('field_link')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_freedom_fighter', function($table)
        {
            $table->dropColumn('gazette_number');
            $table->dropColumn('mobile');
            $table->dropColumn('sector');
            $table->dropColumn('union');
            $table->text('field_link')->nullable(false)->change();
        });
    }
}
