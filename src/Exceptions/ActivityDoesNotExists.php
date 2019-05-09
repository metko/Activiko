<?php

namespace Metko\Activiko\Exceptions;

use InvalidArgumentException;

class ActivityDoesNotExists extends InvalidArgumentException
{
    public static function withIndex(string $index)
    {
        return new static("There is no index named `{$index}` in the changes.");
    }
}
