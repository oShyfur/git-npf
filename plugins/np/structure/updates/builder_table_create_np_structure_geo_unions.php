<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureGeoUnions extends Migration
{
    public function up()
    {
        Schema::create('np_structure_geo_unions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name_eng', 255)->nullable();
            $table->string('name_bng', 255)->nullable();
            $table->string('bbs_code', 20)->nullable();
            $table->boolean('status')->default(1);
            $table->integer('geo_division_id');
            $table->integer('geo_district_id');
            $table->integer('geo_upazila_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_structure_geo_unions');
    }
}
