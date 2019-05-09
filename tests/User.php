<?php

namespace Metko\Activiko\Tests;

use Illuminate\Support\Str;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authorizable,
        Authenticatable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'users';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            $random = Str::random(2);
            $role->name = 'user_'.$random;
            $role->email = 'user_'.$random.'@test.com';
            $role->password = 'password';
        });

        static::updating(function ($role) {
            $random = Str::random(2);
            $role->name = 'user_'.$random;
            $role->password = 'password';
        });
    }
}
