<?php

/**
 * Created by Administrator.
 * Author: Administrator
 * Date: 2018/3/1
 */
class RolesController  extends Rest
{
    public function indexAction()
    {
        Yaf_Dispatcher::getInstance()->disableView();
        $url = $this->_request->getRequestUri();
        $this->getView()
            ->assign('url', $url);
        $this->display('index');
    }

    public function GET_getRolesAction()
    {
        Input::get('perPage', $perPage, null, 15);
        Input::get('page', $page, null, 1);

        $roles = RolesModel::page($page, $perPage)->select('*');

        foreach ($roles as &$role) {
            $menuIds = [];
            $menuNames = '';
            $menus = RoleMenuModel::where('role_id', $role['id'])->select('*');
            foreach ($menus as $k => $menu) {
                if($k === 0) {
                    $menuNames .= $menu['menu_name'];
                }else {
                    $menuNames .= '，' . $menu['menu_name'];
                }
                array_push($menuIds, $menu['menu_id']);
            }
            $role['menuIds'] = $menuIds;
            $role['menuNames'] = $menuNames;
        }

        $total = RolesModel::count();

        echo json_encode(array(
            'total' => $total,
            'data' => $roles
        ));
    }
}