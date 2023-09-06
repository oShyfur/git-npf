<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDivcomSection extends Migration
{
    public function up()
    {
        Schema::create('np_contents_divcom_section', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->text('field_contact')->nullable();
            $table->text('field_dcsection_forms_nid')->nullable();
            $table->text('field_dc_section_citizen_service')->nullable();
            $table->text('field_dc_section_current_project')->nullable();
            $table->text('field_dc_section_duties')->nullable();
            $table->text('field_dc_section_others')->nullable();
            $table->text('field_law_policy_nid')->nullable();
            $table->text('field_meeting_nid')->nullable();
            $table->text('field_protibedon_nid')->nullable();
            $table->text('field_section_oc_nid')->nullable();
            $table->text('field_staff_office_nid')->nullable();
            $table->smallInteger('lookup')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_divcom_section');
    }
}
