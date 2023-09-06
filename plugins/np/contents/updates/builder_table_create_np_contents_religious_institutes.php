<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsReligiousInstitutes extends Migration
{
    public function up()
    {
        Schema::create('np_contents_religious_institutes', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->smallInteger('religious_institutes')->nullable();
            $table->text('field_history')->nullable();
            $table->text('field_head_person_name')->nullable();
            $table->text('field_head_person_designation')->nullable();
            $table->text('field_head_person_phone')->nullable();
            $table->text('field_head_person_mobile')->nullable();
            $table->text('field_head_person_email')->nullable();
            $table->text('field_religious_ins_contact')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_religious_institutes');
    }
}
