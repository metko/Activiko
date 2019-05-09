<?php

namespace Metko\Activiko;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Metko\Activiko\Skeleton\SkeletonClass
 */
class ActivikoFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'activiko';
    }
}
