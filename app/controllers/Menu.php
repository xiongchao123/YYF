<?php
/**
 * YYF - A simple, secure, and efficient PHP RESTful Framework.
 *
 * @link https://github.com/YunYinORG/YYF/
 *
 * @license Apache2.0
 * @copyright 2015-2017 NewFuture@yunyin.org
 */

/**
 * Demo 示例
 */
class MenuController extends Rest
{

    public function getMenusAction()
    {
        $user_id = \Util\Hashids::getInstance()->decode($_SESSION['uid'])[0];

        $role = UserRoleModel::find(['user_id' => $user_id])->get();

        $role_menus = RoleMenuModel::where('role_id', $role['role_id'])->select('*');

        $menuIDs = [];
        foreach ($role_menus as $role_menu) {
            $menuIDs[] = $role_menu['menu_id'];
        }

        $temp = '(';
        foreach ($menuIDs as $menuID) {
            $temp .= $menuID . ',';
        }
        $temp = trim($temp, ',') . ')';

        $sql = "SELECT * FROM menus WHERE id IN $temp ORDER BY sort;";
        $parents = DB::query($sql);
//        $parents = MenusModel::where('id', 'IN', $temp)->order('sort')->select('*');

        $menus = array();

        foreach ($parents as $parent) {
            $parent['children'] = MenusModel::where('parent_id', $parent['id'])->order('sort')->select('*');

            $menus[] = $parent;
        }
        echo json_encode($menus);
    }

    /**
     * 获取所有Menu
     */
    public function GET_indexAction()
    {
        $parents = MenusModel::where('parent_id', 0)->order('sort')->select('*');
        $res = [];

        foreach ($parents as $parent) {
            array_push($res, $parent);
            $submenus = MenusModel::where('parent_id', $parent['id'])->order('sort')->select('*');

            foreach ($submenus as $submenu) {
                array_push($res, $submenu);
            }
        }
        echo json_encode($res);
    }

}
