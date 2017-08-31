<?php

namespace Skychf\Api;

trait RoleTrait
{
    public function menus()
    {
        return $this->belongsToMany('App\Menu', 'role_menu');
    }
}