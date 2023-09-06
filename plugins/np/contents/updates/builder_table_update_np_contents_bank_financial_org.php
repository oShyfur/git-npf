<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsBankFinancialOrg extends Migration
{
    public function up()
    {
        Schema::table('np_contents_bank_financial_org', function($table)
        {
            $table->text('field_bank_atm_location')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_bank_financial_org', function($table)
        {
            $table->text('field_bank_atm_location')->nullable(false)->change();
        });
    }
}
