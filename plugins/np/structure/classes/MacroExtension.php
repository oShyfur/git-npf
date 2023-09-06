<?php

namespace Np\Structure\Classes;

use October\Rain\Database\Schema\Blueprint;

class MacroExtension
{

    public function boot()
    {
        $this->blueprintMacto();
    }

    public function blueprintMacto()
    {
        Blueprint::macro('auditable', function () {

            $this->timestamps();
            $this->softDeletes();
            $this->unsignedInteger('created_by')->nullable()->index();
            $this->unsignedInteger('updated_by')->nullable()->index();
            $this->unsignedInteger('deleted_by')->nullable()->index();
        });

        Blueprint::macro('contentable', function () {

            $this->string('slug', 512);
            $this->dateTime('publish_date')->nullable();
            $this->dateTime('archive_date')->nullable();
            $this->boolean('publish')->default(1);
            $this->boolean('is_right_side_bar')->default(1);
            $this->integer('site_id');
            $this->unique(['slug', 'site_id']);
        });
    }
}
