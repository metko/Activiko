<?php

namespace Metko\Activiko;

class Activiko
{
    public $oldAttributes = [];
    protected static $neverRecord = ['updated_at', 'created_at', 'id'];
    protected $disableRecord = false;
    protected $exludeOfRecords;
    protected $onlyRecordEvent;

    public $hasAttributesRelation = [];
    protected $attributesChanges = [];

    /**
     * recordActivity.
     *
     * @param mixed $description
     * @param mixed $changes
     */
    public function recordActivity($model, $description, $event, $change = null)
    {
        //dump($event.' ____ '.$this->onlyRecordEvent);
        //dd($this->onlyRecordEvent);
        //dump(in_array($event, $this->onlyRecordEvent));
        if ($this->onlyRecordEvent) {
            if (!in_array($event, $this->onlyRecordEvent)) {
                return $this;
            }
        }
        if ($this->disableRecord == true) {
            return $this;
        }
        //dump($event);

        $model->activities()->create([
           'visibility' => $this->getVisibility(),
           'user_id' => $this->getAuthUser(),
           'description' => $description,
           'change' => $change ?? $this->getChangedFields($model),
       ]);
        //dd($this->activities);
    }

    /**
     * getVisibility.
     */
    protected function getVisibility()
    {
        if (isset(static::$activityVisibility)) {
            return static::$activityVisibility;
        }

        return 'default';
    }

    public function getAuthUser()
    {
        return auth()->user()->id ?? 0;
    }

    /**
     * activityChanges.
     */
    protected function getChangedFields($model)
    {
        $this->attributesChanges = [
            'before' => [], 'after' => [],
        ];
        if ($model->wasChanged()) {
            $this->attributesChanges['before'] = array_except(array_diff($this->oldAttributes, $model->getAttributes()), array_merge(self::$neverRecord, $this->exludeOfRecords ?? []));
            $this->attributesChanges['after'] = array_except($model->getChanges(), array_merge(self::$neverRecord, $this->exludeOfRecords ?? []));
        } else {
            $this->attributesChanges['after'] = array_except($model->getAttributes(), array_merge(self::$neverRecord, $this->exludeOfRecords ?? []));
        }
        $this->recordRelation();

        return $this->attributesChanges;
    }

    /**
     * recordOldAttributes.
     *
     * @param mixed $name
     * @param mixed $attributes
     * @param mixed $fields
     */
    public function recordOldAttributes($name, $attributes, $fields = [])
    {
        if ($attributes instanceof Collection) {
            foreach ($attributes as $attribute) {
                $this->hasAttributesRelation[$name][] = count($fields) > 0 ? $attribute->only($fields) : $attribute->getAttributes();
                if (count($fields)) {
                    $this->hasAttributesRelation[$name]['fields'] = $fields;
                }
            }
        } else {
            $this->hasAttributesRelation[$name][] = count($fields) > 0 ? $attributes->only($fields) : $attributes->getAttributes();
        }
    }

    /**
     * recordRelation.
     */
    public function recordRelation()
    {
        if (!empty($this->hasAttributesRelation)) {
            foreach ($this->hasAttributesRelation as $model => $value) {
                if (is_null($this->$model || empty($this->$model))) {
                    break;
                }

                if ($this->$model instanceof Collection) {
                    $this->recordManyrelation($this->$model, $model, $value);
                } else {
                    $this->recordOneOneRelation($this->$model, $model);
                }
            }
        }
    }

    /**
     * recordOneOneRelation.
     *
     * @param mixed $oneRelation
     * @param mixed $modelName
     */
    public function recordOneOneRelation($oneRelation, $modelName)
    {
        $recordOldAttributes = array_merge(
      $this->attributesChanges['before'],
         array_except(
            array_diff($this->hasAttributesRelation[$modelName][0], $oneRelation->getAttributes()),
         'updated_at')
      );
        $this->attributesChanges['before'] = $recordOldAttributes;
        $this->attributesChanges['after'] = array_merge($this->attributesChanges['after'], array_except($oneRelation->getChanges(), 'updated_at'));
    }

    /**
     * recordManyRelation.
     *
     * @param mixed $manyRelation
     * @param mixed $modelName
     * @param mixed $value
     */
    public function recordManyRelation($manyRelation, $modelName, $value)
    {
        $newManyAttributes = [];
        foreach ($manyRelation as $k) {
            $newManyAttributes[] = $k->only($value['fields']);
        }
        $oldManyAttributes = Arr::except($this->hasAttributesRelation[$modelName], ['fields']);
        if ($newManyAttributes !== $oldManyAttributes) {
            $this->attributesChanges['before'][$modelName] = $oldManyAttributes;
            $this->attributesChanges['after'][$modelName] = $newManyAttributes;
        }
    }

    public function disable()
    {
        return $this->disableRecord = true;
    }

    public function disableFields($fields)
    {
        $this->exludeOfRecords = array_merge($this->exludeOfRecords ? $this->exludeOfRecords : [], $fields);

        return $this;
    }

    public function onlyRecordsEvents($events)
    {
        //dump('only record event'.$events[0]);
        $this->onlyRecordEvent = $events;
    }
}
