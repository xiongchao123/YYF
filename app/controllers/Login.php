<?php

class LoginController extends Rest
{
    public function loginAction()
    {
        //判断当前用户是否已经登录
        if(isset($_SESSION['uid'])){
            $uid=\Util\Hashids::getInstance()->decode($_SESSION['uid']);
            if($uid && UsersModel::where("id",$uid[0])->select("id")){
                $this->redirect("/mine");
            }
        }
        Yaf_Dispatcher::getInstance()->enableView();
        $this->getView();
    }

    public function registerAction()
    {
        Yaf_Dispatcher::getInstance()->enableView();
    }

    public function POST_loginAction()
    {
        if (!Input::post("email", $email) || !$email) {
            return $this->throwErrorResponse('login', ['email' => 'email必须填写']);
        }
        if (!Input::post("password", $password) || !$password) {
            return $this->throwErrorResponse('login', ['password' => 'password必须填写']);
        }
        $user=UsersModel::where('email',$email)->select();

        if($user && password_verify($password,$user[0]['password'])){
            //保存session跳转后台
            Session::set("uid",\Util\Hashids::getInstance()->encode($user[0]['id']));
            $this->redirect("/mine");
        }else{
            return $this->throwErrorResponse('login', ['password' => Config::getLangZh('auth', 'failed'),'email'=>Config::getLangZh('auth', 'failed')]);
        }

    }

    /**
     * register user
     * @throws Exception
     */
    public function POST_registerAction()
    {
        if (!Input::post("name", $name) || !$name) {
            return $this->throwErrorResponse('register', ['name' => 'name必须填写']);
        }
        if (!Input::post("phone", $phone) || !$phone) {
            return $this->throwErrorResponse('register', ['phone' => 'phone必须填写']);
        }
        if (!Input::post("email", $email) || !$email) {
            return $this->throwErrorResponse('register', ['email' => 'email必须填写']);
        }
        if (!Input::post("password", $password) || !$password) {
            return $this->throwErrorResponse('register', ['password' => 'password必须填写']);
        }
        if (!Input::post("password_confirmation", $password_confirmation) || !$password_confirmation) {
            return $this->throwErrorResponse('register', ['password_confirmation' => 'password_confirmation必须填写']);
        }
        foreach (validate(['phone' => $phone]) as $k => $v) {
            if (!$v) {
                return $this->throwErrorResponse('register', [$k => $k . "格式不正确!"]);
            }
        };
        foreach (validate(['email' => $email]) as $k => $v) {
            if (!$v) {
                return $this->throwErrorResponse('register', [$k => $k . "格式不正确!"]);
            }
        };
        //password
        if (strlen($password) < 6) {
            return $this->throwErrorResponse('register', ['password' => Config::getLangZh('passwords', 'password')]);
        }
        if (!($password === $password_confirmation)) {
            return $this->throwErrorResponse('register', ['password_confirmation' => Config::getLangZh('passwords', 'password_confirmation')]);
        }
        //insert user to database
        $id = UsersModel::insert([
            'name' => $name,
            'userphone' => $phone,
            'email' => $email,
            'password' => $this->password_hash($password),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        UserRoleModel::insert(array(
            'user_id' => $id,
            'role_id' => 2,
        ));
        //保存session跳转后台
        Session::set("uid",\Util\Hashids::getInstance()->encode($id));
        $this->redirect("/mine");
    }

    public function logoutAction()
    {
        try {
            Session::del('uid');

            echo json_encode(array(
                'status' => 0,
                'message' => '退出成功'
            ));
        }catch (\Exception $exception) {
            echo json_encode(array(
                'status' => 1,
                'message' => '退出失败'
            ));
        }
    }


    private function throwErrorResponse($tpl, $errors)
    {
        Yaf_Dispatcher::getInstance()->disableView();
        $this->getView()->assign('errors', $errors);
        $this->display($tpl);
    }

    /**
     * @param $password
     * @return mixed
     */
    private function password_hash($password)
    {
        $options = [
            'cost' => 9,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

}
