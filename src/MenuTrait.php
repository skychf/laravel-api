<?php

namespace Skychf\Api;

use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

trait MenuTrait
{
    use SoftDeletes;

    /**
     * 获取分级菜单
     * @author skychf skychf@qq.com
     * @param  Object  $menus
     * @param  integer $id        menu_id
     * @param  boolean $isAdmin
     * @param  array   $userMenus
     * @return Array
     */
    public function getRank($menus = null, $id = 0, $isAdmin = false, $userMenus = [], $withTrashed = false)
    {
        $result = [];
        if (!isset($menus)) {
            $user = Auth::user();
            if ($user) {
                $isAdmin   = $user->isAdmin();
                $userMenus = $user->getMenu();
            }

            $menus = $this->selectRaw('id, menu_id, name, route, ISNULL(deleted_at) as status')->orderBy('sort', 'asc');
            $menus = $withTrashed ? $menus->withTrashed()->get() : $menus->get();
        }
        foreach ($menus as $menu) {
            if ($menu->menu_id != $id || !($isAdmin || in_array($menu->route, $userMenus))) {
                continue;
            }

            $menu->menus = $this->getRank($menus, $menu->id, $isAdmin, $userMenus, $withTrashed);
            $result[]    = $menu;
        }
        return $result;
    }

    /**
     * 获取包括删除的数据
     * @author skychf skychf@qq.com
     * @param  Object  $menus
     * @param  integer $id        menu_id
     * @param  boolean $isAdmin
     * @param  array   $userMenus
     * @return Array
     */
    public function getTrashedRank($menus = null, $id = 0, $isAdmin = false, $userMenus = [])
    {
        return $this->getRank($menus = null, $id = 0, $isAdmin = false, $userMenus = [], $withTrashed = true);
    }
}