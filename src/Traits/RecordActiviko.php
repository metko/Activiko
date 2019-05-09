<?php

namespace Metko\Activiko\Traits;

use Metko\Activiko\Models\Activiko;
use Metko\Activiko\Exceptions\ActivityDoesNotExists;

trait RecordActiviko
{
    public $oldAttributes = [];

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
                app('activiko')->recordActivity($model, $model->activityDescription($event), $event);
            });
            if ($event === 'updated') {
                static::updating(function ($model) {
                    app('activiko')->oldAttributes = $model->getOriginal();
                });
            }
        }
        app('activiko')->disableFields(self::$excludeOfRecords ?? []);
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
     * recordableEvents.
     */
    protected static function recordableEvents()
    {
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        }

        return ['created', 'updated', 'deleted'];
    }

    public function disableFields($fields)
    {
        app('activiko')->disableFields($fields);
    }

    /**
     * lastChanges.
     *
     * @param mixed $fields
     * @param mixed $at
     */
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
}
