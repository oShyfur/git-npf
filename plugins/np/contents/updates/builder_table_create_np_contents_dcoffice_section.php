<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDcofficeSection extends Migration
{
    public function up()
    {
        Schema::create('np_contents_dcoffice_section', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->longText('body')->nullable();
            $table->longText('field_contact')->nullable();
            $table->longText('field_dcsection_forms_nid')->nullable();
            $table->longText('field_dc_section_citizen_service')->nullable();
            $table->longText('field_dc_section_current_project')->nullable();
            $table->longText('field_dc_section_duties')->nullable();
            $table->longText('field_dc_section_others')->nullable();
            $table->longText('field_law_policy_nid')->nullable();
            $table->longText('field_meeting_nid')->nullable();
            $table->longText('field_protibedon_nid')->nullable();
            $table->longText('field_section_oc_nid')->nullable();
            $table->longText('field_staff_office_nid')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_dcoffice_section');
    }
}
