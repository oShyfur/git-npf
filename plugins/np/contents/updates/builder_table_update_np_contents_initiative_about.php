<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsInitiativeAbout extends Migration
{
    public function up()
    {
        Schema::rename('np_contents_initiatives_about', 'np_contents_initiative_about');
    }
    
    public function down()
    {
        Schema::rename('np_contents_initiative_about', 'np_contents_initiatives_about');
    }
}
