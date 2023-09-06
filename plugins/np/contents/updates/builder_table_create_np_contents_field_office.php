<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsFieldOffice extends Migration
{
    public function up()
    {
        Schema::create('np_contents_field_office', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->longText('body')->nullable();
            $table->text('field_office_cism')->nullable();
            $table->text('field_process_maps_nid')->nullable();
            $table->text('field_citizen_charter')->nullable();
            $table->text('field_important_info')->nullable();
            $table->text('field_e_directory_nid');
            $table->text('field_info_officer_nid')->nullable();
            $table->text('field_staff_office_nid')->nullable();
            $table->text('field_notice_node_ref_nid')->nullable();
            $table->text('field_ref_gov_download_nid')->nullable();
            $table->text('field_law_circular_nid')->nullable();
            $table->text('field_photo_gallery_office_nid')->nullable();
            $table->text('field_projects')->nullable();
            $table->text('field_address');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_field_office');
    }
}
