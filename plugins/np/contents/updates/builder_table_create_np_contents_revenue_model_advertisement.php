<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsRevenueModelAdvertisement extends Migration
{
    public function up()
    {
        Schema::create('np_contents_revenue_model_advertisement', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body');
            $table->boolean('field_is_slide')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_revenue_model_advertisement');
    }
}
