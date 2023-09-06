<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsEducationInstitute extends Migration
{
    public function up()
    {
        Schema::create('np_contents_education_institute', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->longText('title');
            $table->longText('body')->nullable();
            $table->longText('field_edu_achievements')->nullable();
            $table->longText('field_edu_future_plan')->nullable();
            $table->longText('field_edu_outstanding_students')->nullable();
            $table->longText('field_edu_scholarship_info')->nullable();
            $table->longText('field_history')->nullable();
            $table->longText('field_passing_rate')->nullable();
            $table->longText('field_student_class')->nullable();
            $table->longText('field_managing_committee')->nullable();
            $table->longText('field_last_five_yrs_result')->nullable();
            $table->longText('field_established_text')->nullable();
            $table->longText('field_total_students')->nullable();
            $table->longText('field_contact')->nullable();
            $table->longText('field_description')->nullable();
            $table->longText('field_photo_gallery_office_nid')->nullable();
            $table->longText('field_e_directory_teachers_profile_nid')->nullable();
            $table->longText('field_e_directory_headmaster_principal_nid')->nullable();
            $table->smallInteger('institute_type')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_education_institute');
    }
}
