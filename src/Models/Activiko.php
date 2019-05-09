<?php

namespace Metko\Activiko\Models;

use Illuminate\Database\Eloquent\Model;

class Activiko extends Model
{
    protected $table = 'activiko';

    protected $fillable = ['visibility', 'description', 'change'];

    protected $casts = [
        'change' => 'array',
    ];
}
