<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsHospitalclinic2 extends Migration
{
    public function up()
    {
        Schema::create('np_contents_hospitalclinic', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('field_hospitalclinic_type', 10);
            $table->text('field_hospitalclinic_address')->nullable();
            $table->text('field_hospitalclinic_doctor_nid')->nullable();
            $table->text('field_hospitalclinic_serv_list')->nullable();
            $table->text('field_contact_person_name')->nullable();
            $table->text('field_contact_person_designation')->nullable();
            $table->text('field_contact_person_phone')->nullable();
            $table->text('field_contact_person_mobile')->nullable();
            $table->text('field_contact_person_email')->nullable();
            $table->text('field_hospitalclinic_ave_patient')->nullable();
            $table->date('field_hospitalclinic_estd')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_hospitalclinic');
    }
}
