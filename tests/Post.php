<?php

namespace Metko\Activiko\Tests;

use Illuminate\Database\Eloquent\Model;
use Metko\Activiko\Traits\RecordActiviko;

class Post extends Model
{
    use RecordActiviko;

    protected $guarded = [];
    //protected static $recordableEvents = ['updated'];
}
