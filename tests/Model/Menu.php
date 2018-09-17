<?php

namespace Nestable\Tests\Model;

use Illuminate\Database\Eloquent\Model;
use Nestable\NestableTrait;

class Menu extends Model
{
    use NestableTrait;

    protected $table = 'menus';

    protected $parent = 'pid';

    protected $fillable = [
        'id',
        'pid',
        'name',
        'icon',
        'permission_id',
        'url',
        'active',
        'sort',
        'description',
        'status'
    ];
}
