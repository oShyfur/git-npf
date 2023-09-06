<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1036 extends Migration
{
    public function up()
    {
        Schema::table('backend_users', function($table)
         {
             $table->text('allowed_ct')->nullable();
         });
    }

    public function down()
    {
        Schema::table('backend_users', function($table)
         {
             $table->dropColumn('allowed_ct');
         });
    }
}