<?php namespace Np\Contents\Traits;

use Db;
use Exception;
use DateTime;
use Illuminate\Database\Eloquent\Model as EloquentModel;

trait Revisionable
{

    /**
     * @var array List of attributes to monitor for changes and store revisions for.
     *
     * protected $revisionable = [];
     */

    /**
     * @var int Maximum number of revision records to keep.
     *
     * public $revisionableLimit = 500;
     */

    /*
     * You can change the relation name used to store revisions:
     *
     * const REVISION_HISTORY = 'revision_history';
     */

    /**
     * @var bool Flag for arbitrarily disabling revision history.
     */
    public $revisionsEnabled = true;

    /**
     * Boot the revisionable trait for a model.
     * @return void
     */
    public static function bootRevisionable()
    {

        static::extend(function ($model) {

            //relations
            $model->morphMany['revision_history'] = ['Np\Contents\Models\ContentRevision', 'name' => 'revisionable'];


            //events
            $model->bindEvent('model.afterUpdate', function () use ($model) {
                $model->revisionableAfterUpdate($model);
            });
            $model->bindEvent('model.afterDelete', function () use ($model) {
                $model->revisionableAfterDelete($model);
            });
        });
    }

    public function revisionableAfterUpdate($model)
    {

        $relation = $this->getRevisionHistoryName();
        $relationObject = $model->{$relation}();
        $revisionModel = $relationObject->getRelated();



        $toSave = [
            'fields' => $model->toArray()
        ];

        $relationObject->create($toSave);

        //Db::connection($revisionModel->connection)->table($revisionModel->getTable())->insert($toSave);
        $this->revisionableCleanUp();
    }

    public function revisionableAfterDelete($model)
    {
        $relation = $this->getRevisionHistoryName();
        $relationObject = $this->{$relation}();
        $relationObject->delete();
    }

    /*
     * Deletes revision records exceeding the limit.
     */
    protected function revisionableCleanUp()
    {
        $relation = $this->getRevisionHistoryName();
        $relationObject = $this->{$relation}();

        $revisionLimit = property_exists($this, 'revisionableLimit')
            ? (int)$this->revisionableLimit
            : 5;

        $toDelete = $relationObject
            ->orderBy('created_at', 'desc')
            ->skip($revisionLimit)
            ->limit(64)
            ->get();

        foreach ($toDelete as $record) {
            $record->delete();
        }
    }

    /**
     * Get revision history relation name name.
     * @return string
     */
    public function getRevisionHistoryName()
    {
        return 'revision_history';
    }
}
