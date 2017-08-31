<?php

namespace Skychf\Api;

trait UserTrait
{
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    /**
     * 是否有菜单权限
     * @author skychf skychf@qq.com
     * @param  array   $menus
     * @return boolean
     */
    public function hasMenu($menus = [])
    {
        if (is_array($menus)) {
            foreach ($menus as $menu) {
                $hasMenu = $this->hasMenu($menu);
                if (! $hasMenu) {
                    return false;
                }
            }

            return true;
        } else {

            if ($this->hasRole(1)) return true;

            $user_menus = $this->getMenu();

            if (in_array($menus, $user_menus)) return true;

            return false;
        }
    }
    /**
     * 获取用户菜单
     * @author skychf skychf@qq.com
     * @return Array
     */
    public function getMenu($user_id = null)
    {
        $routes = [];

        foreach ($this->roles()->get() as $role) {
            $menus = $role->menus()->pluck('route')->toArray();
            $routes = array_merge($routes, $menus);
        }

        return $routes;
    }

    /**
     * 是否有角色ID
     * @author skychf skychf@qq.com
     * @param  Integer  $role_id
     * @return boolean
     */
    public function hasRole($role_id)
    {
        $roles = $this->roles()->pluck('role_id')->toArray();
        return in_array($role_id, $roles);
    }

    /**
     * 是否是管理员
     * @author skychf skychf@qq.com
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->hasRole(1);
    }
}