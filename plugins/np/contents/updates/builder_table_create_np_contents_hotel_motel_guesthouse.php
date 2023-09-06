<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsHotelMotelGuesthouse extends Migration
{
    public function up()
    {
        Schema::create('np_contents_hotel_motel_guesthouse', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('institute_govt_nongovt', 10);
            $table->text('field_hotel_proprietor_name')->nullable();
            $table->text('field_contact_person_name')->nullable();
            $table->text('field_contact_person_designation')->nullable();
            $table->text('field_contact_person_phone')->nullable();
            $table->text('field_hotel_mobile')->nullable();
            $table->text('field_contact_person_email')->nullable();
            $table->text('field_hotel_address')->nullable();
            $table->text('field_hotel_place')->nullable();
            $table->text('field_room_description')->nullable();
            $table->text('website')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_hotel_motel_guesthouse');
    }
}
