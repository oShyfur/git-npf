   //Versionable
   use \Np\Contents\Traits\Revisionable;
   protected $revisionable = {versionableColumns};
   public $revisionableLimit = {versionLimit};
   public $morphMany = [
        'revision_history' => ['Np\Contents\Models\ContentRevision', 'name' => 'revisionable']
    ];