<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1040 extends Migration
{
    public function up()
    {
        Schema::table('system_mail_templates', function ($table) {
            $table->text('content_html_en')->nullable();
        });
    }

    public function down()
    {
        Schema::table('system_mail_templates', function ($table) {
            $table->dropColumn('content_html_en');
        });
    }
}
