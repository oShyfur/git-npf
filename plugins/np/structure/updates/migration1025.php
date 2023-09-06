<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1025 extends Migration
{
    public function up()
    {
        Schema::table('backend_users', function($table)
        {
            $table->string('designation',200)->nullable();
            $table->string('phone',200)->nullable();
        });
    }

    public function down()
    {
        
        Schema::table('backend_users', function($table)
        {
            $table->dropColumn('designation');
            $table->dropColumn('phone');
        });
        
        
    }
}