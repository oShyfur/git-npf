<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsTcvDetails extends Migration
{
    public function up()
    {
        Schema::create('np_contents_tcv_details', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('tcv_about')->nullable();
            $table->integer('time_number')->default(0);
            $table->integer('cost_number')->default(0);
            $table->integer('value_number')->default(0);
            $table->text('bsd_about')->nullable();
            $table->integer('bsd_number')->default(0);
            $table->text('bvs_about')->nullable();
            $table->integer('bvs_number')->default(0);
            $table->text('bmds_about')->nullable();
            $table->integer('bmds_number')->default(0);
            $table->text('bsc_about')->nullable();
            $table->integer('bsc_number')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_tcv_details');
    }
}
