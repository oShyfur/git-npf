<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsFrontServiceBox extends Migration
{
    public function up()
    {
        Schema::rename('np_contents_service_box', 'np_contents_front_service_box');
    }
    
    public function down()
    {
        Schema::rename('np_contents_front_service_box', 'np_contents_service_box');
    }
}
