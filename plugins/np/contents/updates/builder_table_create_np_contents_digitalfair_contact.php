<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDigitalfairContact extends Migration
{
    public function up()
    {
        Schema::create('np_contents_digitalfair_contact', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('digitalfair_year', 10);
            $table->text('agriculture_email')->nullable();
            $table->text('agriculture_mobile')->nullable();
            $table->text('health_email')->nullable();
            $table->text('health_mobile')->nullable();
            $table->text('land_email')->nullable();
            $table->text('land_mobile')->nullable();
            $table->text('utility_email')->nullable();
            $table->text('utility_mobile')->nullable();
            $table->text('license_certificate_passport_email')->nullable();
            $table->text('license_certificate_passport_mobile')->nullable();
            $table->text('law_email')->nullable();
            $table->text('law_mobile')->nullable();
            $table->text('others_email')->nullable();
            $table->text('others_mobile')->nullable();
            $table->text('dcenter_institute_email')->nullable();
            $table->text('dcenter_institute_mobile')->nullable();
            $table->text('startup_inventor_email')->nullable();
            $table->text('startup_inventor_mobile')->nullable();
            $table->text('education_email')->nullable();
            $table->text('education_mobile')->nullable();
            $table->text('employment_email')->nullable();
            $table->text('employment_mobile')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_digitalfair_contact');
    }
}
