<?php

namespace Metko\Activiko\Tests;

use Illuminate\Database\Eloquent\Model;
use Metko\Activiko\Traits\RecordActiviko;

class Post2 extends Model
{
    use RecordActiviko;
    protected $table = 'posts';
    protected $guarded = [];
    protected static $recordableEvents = ['updated'];
    protected $excludeOfRecords = ['body'];
}
