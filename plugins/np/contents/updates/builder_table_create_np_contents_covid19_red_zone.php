<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsCovid19RedZone extends Migration
{
    public function up()
    {
        Schema::create('np_contents_covid19_red_zone', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('area')->nullable();
            $table->date('date');
            $table->string('days');
            $table->text('test_center')->nullable();
            $table->text('ambulance')->nullable();
            $table->text('tele_medicine')->nullable();
            $table->text('isolation_center')->nullable();
            $table->text('covid_hospital')->nullable();
            $table->text('noncovid_hospital')->nullable();
            $table->text('emergency_number')->nullable();
            $table->text('further_progress')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_covid19_red_zone');
    }
}
