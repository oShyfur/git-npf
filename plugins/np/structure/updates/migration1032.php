<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1032 extends Migration
{
   public function up()
    {
        Schema::table('backend_users', function($table)
         {
             $table->boolean('is_sso')->default(false);
         });
    }

    public function down()
    {
        Schema::table('backend_users', function($table)
         {
             $table->dropColumn('is_sso');
         });
    }
}