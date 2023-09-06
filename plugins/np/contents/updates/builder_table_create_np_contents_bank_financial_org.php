<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsBankFinancialOrg extends Migration
{
    public function up()
    {
        Schema::create('np_contents_bank_financial_org', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('category_of_bank_financial_org', 10);
            $table->text('field_bank_finance_branch_name')->nullable();
            $table->text('field_bank_branch_address');
            $table->text('field_bank_atm_location');
            $table->date('field_bank_finance_estd')->nullable();
            $table->text('field_head_name')->nullable();
            $table->text('field_head_designation')->nullable();
            $table->text('field_head_phone')->nullable();
            $table->text('field_head_mobile')->nullable();
            $table->text('field_head_email')->nullable();
            $table->text('field_contact_name')->nullable();
            $table->text('field_contact_designation')->nullable();
            $table->text('field_contact_phone')->nullable();
            $table->text('field_contact_mobile')->nullable();
            $table->text('field_contact_email')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_bank_financial_org');
    }
}
