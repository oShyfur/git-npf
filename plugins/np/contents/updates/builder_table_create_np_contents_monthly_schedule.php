<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsMonthlySchedule extends Migration
{
    public function up()
    {
        Schema::create('np_contents_monthly_schedule', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('field_agenda');
            $table->dateTime('field_monthly_activity_time')->nullable();
            $table->text('field_monthly_schedule_place')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_monthly_schedule');
    }
}
