<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsSocialMediaCorner extends Migration
{
    public function up()
    {
        Schema::table('np_contents_social_media_corner', function($table)
        {
            $table->text('field_social_media_url')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_social_media_corner', function($table)
        {
            $table->string('field_social_media_url', 255)->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
