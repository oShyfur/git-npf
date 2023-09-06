<?php

namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsRevenueModelJob extends Migration
{
    public function up()
    {
        Schema::create('np_contents_revenue_model_job', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->longText('body')->nullable();
            $table->integer('sort_order');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_contents_revenue_model_job');
    }
}
