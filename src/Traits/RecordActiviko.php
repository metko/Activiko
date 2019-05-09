<?php

namespace Metko\Activiko\Traits;

use Metko\Activiko\Models\Activiko;
use Metko\Activiko\Exceptions\ActivityDoesNotExists;

trait RecordActiviko
{
    public $oldAttributes = [];
    public $hasAttributesRelation = [];
    protected $attributesChanges = [];
    protected static $neverRecord = ['updated_at', 'created_at', 'id'];
    protected $disableRecord = false;

    /**
     * Get activity related to.
     *
     * @return App\Activity morphMany relation
     */
    public function activities()
    {
        return $this->morphMany(Activiko::class, 'subject')->latest();
    }

    /**
     * bootRecordActivityTrait.
     */
    public static function bootRecordActiviko()
    {
        foreach (self::recordableEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($model->activityDescription($event));
            });
            if ($event === 'updated') {
                static::updating(function ($model) {
                    $model->oldAttributes = $model->getOriginal();
                });
            }
        }
    }

    /**
     * recordableEvents.
     */
    protected static function recordableEvents()
    {
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        }

        return ['created', 'updated', 'deleted'];
    }

    /**
     * activityDescription.
     *
     * @param mixed $description
     */
    protected function activityDescription($description)
    {
        //dump($description);
        return "{$description}_".strtolower(class_basename($this));
    }

    /**
     * recordActivity.
     *
     * @param mixed $description
     * @param mixed $changes
     */
    public function recordActivity($description, $change = null)
    {
        if ($this->disableRecord == true) {
            return $this;
        }
        $this->activities()->create([
           'visibility' => $this->getVisibility(),
           'user_id' => $this->getAuthUser(),
           'description' => $description,
           'change' => $change ?? $this->getChangedFields(),
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
    protected function getChangedFields()
    {
        $this->attributesChanges = [
            'before' => [], 'after' => [],
        ];
        //dd($this);
        if ($this->wasChanged()) {
            $this->attributesChanges['before'] = array_except(array_diff($this->oldAttributes, $this->getAttributes()), array_merge(self::$neverRecord, $this->excludeOfRecords ?? []));
            $this->attributesChanges['after'] = array_except($this->getChanges(), array_merge(self::$neverRecord, $this->excludeOfRecords ?? []));
        } else {
            $this->attributesChanges['after'] = array_except($this->getAttributes(), array_merge(self::$neverRecord, $this->excludeOfRecords ?? []));
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

    public function lastChanges($fields = null, $at = 'all')
    {
        $activitie = $this->activities->last()->change;
        if (is_null($fields)) {
            return $activitie;
        }

        if ($fields == 'before') {
            return $activitie['before'];
        } elseif ($fields == 'after') {
            return $activitie['after'];
        }

        //dd(!isset($activities['after'][$fields]));
        if (!isset($activitie['before'][$fields]) && !isset($activitie['after'][$fields])) {
            throw  ActivityDoesNotExists::withIndex($fields);
        }

        $activities['before'] = !empty($activitie['before']) ? $activitie['before'][$fields] : '';
        $activities['after'] = $activitie['after'][$fields];

        if ($at == 'all') {
            return $activities;
        } elseif ($at == 'before') {
            return $activities['before'];
        } elseif ($at == 'after') {
            return $activities['after'];
        } else {
            throw  ActivityDoesNotExists::withIndex($at);
        }
    }

    public function disableRecord()
    {
        return $this->disableRecord = true;
    }
}
