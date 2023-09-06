<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureBlocks3 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_blocks', function ($table) {
            if (!Schema::hasColumn('np_structure_blocks', 'title_color')) {

                $table->string('title_color', 100)->nullable();
            }

            if (!Schema::hasColumn('np_structure_blocks', 'title_bgcolor')) {

                $table->string('title_bgcolor', 100)->nullable();
            }

            if (!Schema::hasColumn('np_structure_blocks', 'show_title')) {

                $table->boolean('show_title')->default(1);
            }
        });
    }

    public function down()
    {
        Schema::table('np_structure_blocks', function ($table) {
            $table->dropColumn('title_color');
            $table->dropColumn('title_bgcolor');
            $table->dropColumn('show_title');
        });
    }
}
