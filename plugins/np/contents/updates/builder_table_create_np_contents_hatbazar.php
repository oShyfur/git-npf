<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsHatbazar extends Migration
{
    public function up()
    {
        Schema::create('np_contents_hatbazar', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->text('field_hat_address')->nullable();
            $table->text('field_hat_revenue')->nullable();
            $table->text('field_hat_authority')->nullable();
            $table->text('field_hat_aioton')->nullable();
            $table->text('field_chandina_viti')->nullable();
            $table->text('field_info_person')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_hatbazar');
    }
}
