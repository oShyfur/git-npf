<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1027 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function ($table) {
            $table->integer('geo_division_id')->nullable();
            $table->integer('geo_district_id')->nullable();
            $table->integer('geo_upazila_id')->nullable();
            $table->integer('geo_union_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('np_structure_sites', function ($table) {

            $table->dropColumn('geo_division_id');
            $table->dropColumn('geo_district_id');
            $table->dropColumn('geo_upazila_id');
            $table->dropColumn('geo_union_id');
        });
    }
}