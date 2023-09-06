<?php

namespace Np\Structure\Classes\Jobs;

use Np\Structure\Classes\NP;
use DB;

abstract class BaseClone
{

    protected $sourceSite;
    protected $destinationSite;
    protected $generateNewId;

    public $connectionSite = null;
    public $items = [];
    public $oldIds = [];
    public $deletedIds = [];

    public $table;


    public function __construct($sourceSite, $destinationSite, $attachmentIds = [], $generateNewId = true)
    {
        $this->sourceSite = $sourceSite;
        $this->destinationSite = $destinationSite;
        $this->generateNewId = $generateNewId;
        $this->attachmentIds = $attachmentIds;
    }

    abstract public function copy();
    abstract public function paste();

    public function oldNewIdMapping()
    {
        return collect($this->oldIds);
    }

    public function getItems()
    {
        return collect($this->items);
    }

    public function getDeletedIds()
    {
        return collect($this->deletedIds);
    }


    public function setConnection($site)
    {
        $this->connectionSite = $site;
        NP::setTenantConnection($this->connectionSite);
        return $this;
    }

    public function setConnectionByArray($configuration)
    {
        $this->connectionSite = 1;
        NP::setTenantConnectionByArray($configuration);
        return $this;
    }

    public function cloneFiles()
    {
        $files = new CloneFiles($this->sourceSite, $this->destinationSite, $this->oldNewIdMapping()->toArray());
        $files->setConnection($this->sourceSite)->copy();
        $files->setConnection($this->destinationSite)->paste();
    }
}
