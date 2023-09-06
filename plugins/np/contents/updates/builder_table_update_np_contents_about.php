<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsAbout extends Migration
{
    public function up()
    {
        Schema::table('np_contents_about', function($table)
        {
            $table->text('slogan');
            $table->text('strategy_details');
            $table->text('strategy_points');
            $table->text('simplify_slogan');
            $table->text('simplify_details');
            $table->text('model_change_details');
            $table->text('model_change_points');
            $table->text('initiative_slogan');
            $table->text('initiative_details');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_about', function($table)
        {
            $table->dropColumn('slogan');
            $table->dropColumn('strategy_details');
            $table->dropColumn('strategy_points');
            $table->dropColumn('simplify_slogan');
            $table->dropColumn('simplify_details');
            $table->dropColumn('model_change_details');
            $table->dropColumn('model_change_points');
            $table->dropColumn('initiative_slogan');
            $table->dropColumn('initiative_details');
        });
    }
}
