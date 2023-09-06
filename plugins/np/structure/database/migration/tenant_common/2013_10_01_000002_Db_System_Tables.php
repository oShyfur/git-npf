<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class DbSystemTables extends Migration
{
    public function up()
    {
        Schema::create('system_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->bigIncrements('id');
            $table->string('disk_name', 512);
            $table->string('file_name')->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->integer('file_size');
            $table->string('content_type');
            $table->mediumText('title')->nullable()->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->text('description')->nullable()->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->string('field')->nullable()->index();
            $table->string('attachment_id')->index()->nullable();
            $table->string('attachment_type')->index()->nullable();
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('rainlab_translate_attributes', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->string('model_id')->index()->nullable();
            $table->string('model_type')->index()->nullable();
            $table->mediumText('attribute_data')->nullable()->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
        });

        Schema::create('rainlab_translate_indexes', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->string('model_id')->index()->nullable();
            $table->string('model_type')->index()->nullable();
            $table->string('item')->nullable()->index();
            $table->mediumText('value')->nullable();
        });


        Schema::create('rainlab_translate_locales', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->string('code')->index();
            $table->string('name')->index()->nullable();
            $table->boolean('is_default')->default(0);
            $table->boolean('is_enabled')->default(0);
        });

        Schema::create('rainlab_translate_messages', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->string('code')->index()->nullable();
            $table->mediumText('message_data')->nullable();
        });

        Schema::create('np_contents_texonomy_types', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id')->unsigned();
            $table->integer('old_id')->nullable();
            $table->text('name')->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->text('description')->nullable()->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->integer('parent_id')->nullable();
            $table->boolean('is_common')->default(0);
            $table->boolean('status')->default(1);
            $table->string('code', 200)->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });

        Schema::create('np_contents_texonomy', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id')->unsigned();
            $table->integer('old_id')->nullable();
            $table->string('name', 200)->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->integer('sort_order')->default(0);
            $table->integer('texonomy_type_id')->nullable()->unsigned();
            $table->integer('site_id')->unsigned()->index();
            $table->bigInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->integer('deleted_by')->nullable()->unsigned();
        });

        Schema::create('system_parameters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');
            $table->string('namespace', 100);
            $table->string('group', 50);
            $table->string('item', 150);
            $table->text('value')->nullable();
            $table->index(['namespace', 'group', 'item'], 'item_index');
        });

        Schema::create('np_contents_blocks', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id')->unsigned();
            $table->string('title', 200)->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->string('region', 20);
            $table->integer('sort_order')->default(0);
            $table->text('body')->nullable()->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->boolean('status')->default(1);
            $table->integer('site_id')->unsigned()->index();
            $table->integer('type')->default(2);
            $table->string('partial_code', 200)->nullable();
            $table->string('predefined_template_code', 200)->nullable();
            $table->string('title_bgcolor', 200)->nullable();
            $table->string('title_color', 200)->nullable();
            $table->boolean('show_title')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });

        Schema::create('np_contents_menus', function ($table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->string('id', 40)->primary();
            $table->tinyInteger('depth', false, true);
            $table->text('title')->charset('utf8mb4')->collate('utf8mb4_unicode_ci')->nullable();
            $table->string('link_type');
            $table->text('link_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('parent_id')->nullable();
            $table->integer('site_id')->unsigned()->index();
            $table->boolean('status')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable()->default(1);
            $table->integer('deleted_by')->nullable();
        });

        Schema::create('content_revisions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->string('id', 40);
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('site_id')->unsigned()->index();
            $table->longText('fields')->nullable()->charset('utf8mb4')->collate('utf8mb4_unicode_ci');
            $table->string('revisionable_type');
            $table->string('revisionable_id', 40);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable()->default(1);
            $table->integer('deleted_by')->nullable();
            $table->index(['revisionable_id', 'revisionable_type', 'site_id'], 'content_revisions_id_type_site_index');
            $table->primary(['id']);
        });

        Schema::create('deferred_bindings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('master_type')->index();
            $table->string('master_field')->index();
            $table->string('slave_type')->index();
            $table->string('slave_id')->index();
            $table->string('session_key');
            $table->boolean('is_bind')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_files');
        Schema::dropIfExists('rainlab_translate_attributes');
        Schema::dropIfExists('rainlab_translate_indexes');
        Schema::dropIfExists('rainlab_translate_locales');
        Schema::dropIfExists('rainlab_translate_messages');
        Schema::dropIfExists('np_structure_texonomy_types');
        Schema::dropIfExists('np_contents_texonomy');
        Schema::dropIfExists('system_parameters');
        Schema::dropIfExists('np_structure_blocks');
        Schema::dropIfExists('np_contents_menus');
        Schema::dropIfExists('content_revisions');
        Schema::dropIfExists('deferred_bindings');
    }
}
