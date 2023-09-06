<?php namespace Np\Structure\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1041 extends Migration
{
    public function up()
    {
       
       DB::table('system_mail_layouts')
            ->where('code', 'notice')
            ->update([
                    
                    'code' => 'theme-default',
                    'name'=>'Theme Default'
                ]);
        
        
         DB::table('system_mail_templates')
            ->where('code', 'page-home-default')
            ->update([
                    
                    'code' => 'theme-default-page-home',
                    
                    'subject'=>'Theme Default Home Page',
                    'description'=>'Theme Default Home Page'
                ]);
                
                
                
         DB::table('system_mail_templates')
            ->where('code', 'page-list-default')
            ->update([
                    
                    'code' => 'theme-default-page-list',
                    
                    'subject'=>'Theme Default List Page',
                    'description'=>'Theme Default List Page'
                ]);
                
                
          DB::table('system_mail_templates')
            ->where('code', 'page-details-default')
            ->update([
                    
                    'code' => 'theme-default-page-details',
                    
                    'subject'=>'Theme Default Details Page',
                    'description'=>'Theme Default Details Page'
                ]);
                
            
    }

    public function down()
    {
       
        DB::table('system_mail_layouts')
            ->where('code', 'theme-default')
            ->update([
                    
                    'code' => 'notice',
                    'name'=>'Theme Default'
                ]);
                
        DB::table('system_mail_templates')
            ->where('code', 'theme-default-page-home')
            ->update([
                    
                    'code' => 'page-home-default',
                    
                    'subject'=>'Theme Default Home Page',
                    'description'=>'Theme Default Home Page'
                ]);
                
         DB::table('system_mail_templates')
            ->where('code', 'theme-default-page-list')
            ->update([
                    
                    'code' => 'page-list-default',
                    'name'=>'Theme Default List Page',
                    'subject'=>'Theme Default List Page',
                    'description'=>'Theme Default List Page'
                ]);
                
                
          DB::table('system_mail_templates')
            ->where('code', 'theme-default-page-details')
            ->update([
                    
                    'code' => 'page-details-default',
                    'name'=>'Theme Default Details Page',
                    'subject'=>'Theme Default Details Page',
                    'description'=>'Theme Default Details Page'
                ]);
                
                
    }
}