<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsInternalEservices extends Migration
{
    public function up()
    {
        Schema::rename('np_contents_internal_esheba', 'np_contents_internal_eservices');
    }
    
    public function down()
    {
        Schema::rename('np_contents_internal_eservices', 'np_contents_internal_esheba');
    }
}
