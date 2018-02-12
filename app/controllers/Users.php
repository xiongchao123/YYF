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
class UsersController extends Rest
{
    public function indexAction()
    {
        Yaf_Dispatcher::getInstance()->disableView();
        $url = $this->_request->getRequestUri();
        $this->getView()
            ->assign('url', $url);
        $this->display('index');
    }

    public function getUsersAction()
    {
        Input::get('page', $page, null, 1);
        Input::get('perPage', $perPage, null, 15);

        $users = UsersModel::page($page, $perPage)->select('*');
        $total = UsersModel::count();

        // 获取每个用户权限
        foreach ($users as &$user) {
            $role = UserRoleModel::where('user_id', $user['id'])->find();
            $user['role'] = $role['role_id']+0;
        }


        echo json_encode(array(
            'total' => $total,
            'data' => $users
        ));
    }

    public function rolesAction()
    {
        $roles = RolesModel::select('*');
        echo json_encode($roles);
    }

    public function rolesEditAction()
    {
        Input::get('id', $uid);
        Input::get('number', $max_container);
        Input::get('role', $role);

        try{
            UserRoleModel::where('user_id', $uid)->update(['role_id' => $role]);
            UsersModel::where('id', $uid)->update(['max_containers' => $max_container]);
            echo json_encode(array(
                'status' => 0,
                'message' => '修改成功',
            ));
        }catch (\Exception $e) {
            echo json_encode(array(
                'status' => 1,
                'message' => '修改失败',
            ));
        }
    }

    public function GET_deleteAction()
    {
        Input::get('id', $uid);

        try {
            UserRoleModel::where('user_id', $uid)->delete();
            UsersModel::delete($uid);

            echo json_encode(array(
                'status' => 0,
                'message' => '删除成功',
            ));
        }catch (\Exception $exception) {
            echo json_encode(array(
                'status' => 1,
                'message' => '删除成功',
            ));
        }
    }
}