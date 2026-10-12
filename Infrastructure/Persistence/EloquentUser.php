<?php

namespace Infrastructure\Persistence;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

/**
 * @property string $email
 * @property string $api_key
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, ...$params)
 * @method static \Illuminate\Database\Eloquent\Model|static create(array $attributes)
 */
class EloquentUser extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;

    protected $table = 'users';
    protected $fillable = ['email', 'api_key'];
    public $timestamps = false;
}
