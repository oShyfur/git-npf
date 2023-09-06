<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1042 extends Migration
{
    public function up()
    {
        Schema::table('backend_users', function ($table) {
            $table->string('old_id', 100)->nullable();
            $table->string('old_password', 200)->nullable();
        });
    }

    public function down()
    {

        Schema::table('backend_users', function ($table) {
            $table->dropColumn('old_id');
            $table->dropColumn('old_password');
        });
    }
}
