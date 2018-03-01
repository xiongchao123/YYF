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
class ContainersController extends Rest
{
    public function GET_indexAction()
    {
        Input::get('page', $page, null, 1);
        Input::get('perPage', $perPage, null, 15);
        Input::get('type', $type, null, 'all');

        $user_id = \Util\Hashids::getInstance()->decode($_SESSION['uid'])[0];

        switch ($type) {
            case 'all':
                $res = ContainersModel::page($page, $perPage)->select('*');
                $total = ContainersModel::count();
                break;
            case 'mine':
                $res = ContainersModel::where('user_id', $user_id)->page($page, $perPage)->select('*');
                $total = ContainersModel::where('user_id', $user_id)->count();
                break;
            default:
                $res = '';
        }

        foreach ($res as &$container) {
            $ports = PortsModel::where('container_id', $container['id'])->select('*');
            $mapPorts = '';
            if($ports) {
                foreach ($ports as $port) {
                    if($port['internal_port'] == 3000) {
                        $terminalPort = $port['external_port'];
                    }
                    $mapPorts .=  '  ' . $port['external_port'] . '->' . $port['internal_port'];
                }
            }

            $container['mapPorts'] = $mapPorts;
            $container['terminalPort'] = $terminalPort ?? '';
            $container['router'] = '/containerDetails/' . $container['id'];
            switch ($container['status']) {
                case 0: $container['state'] = '已停止';break;
                case 1: $container['state'] = '运行中';break;
            }
        }

        echo json_encode(array(
            'total' => $total,
            'data' => $res
        ));
    }

    public function createAction()
    {
        $url = $this->_request->getRequestUri();
        Yaf_Dispatcher::getInstance()->enableView();
        $this->getView()->assign('url', $url);
    }

    // 限制创建容器的数量
    protected function maxContainer($uid) {
        $user = UsersModel::find($uid);
        $max = $user->max_containers;

        $count = ContainersModel::where('user_id', $uid)->count();

        return ($count < $max) ? true : false;
    }

    // 递归创建22的随机端口
    protected function created22port() {
        $default_port_22 = mt_rand(1000, 6000);
        $max_port = 7;

        $num = PortsModel::where('external_port', $default_port_22)->count();

        if($num >= $max_port) {
            $this->created22port();
        }else {
            return $default_port_22;
        }
    }

    public function GET_createContainerAction()
    {
//        echo '<pre>';
//        Input::get('page', $page, null, 1);
        $user_id = \Util\Hashids::getInstance()->decode($_SESSION['uid'])[0];

        if(!$this->maxContainer($user_id)) {
            return response()->json(array(
                'status' => 1,
                'message' => '创建容器已超上限'
            ));
        }


        $temp = new \stdClass();
        $temp->port = [];
//        $ports = $request->input('ports');
        Input::get('ports', $ports);

        foreach ($ports as &$port) {
            $port = json_decode($port, true);
        }

        $key = 'default_port';
//        $redis = Redis::connection('ports');
        $redis = new Redis();
        $redis->connect('10.10.83.175', 6378);
        $redis->select(11);


        if($redis->exists($key)) {
            $default_port_3000 = intval($redis->get($key)) + 1;
            $default_port_80 = intval($redis->get($key)) + 2;
//            $redis->set($key, $default_port_22);
        }else {
            $default_port_3000 = 30000;
            $default_port_80 = 30001;
//            $redis->set($key, $default_port_22);
        }

        $ports[] = array(
            "external" => "$default_port_3000",
            "internal" => "3000"
        );
        $ports[] = array(
            "external" => "$default_port_80",
            "internal" => "80"
        );

        $default_port_22 = $this->created22port();
        $ports[] = array(
            "external" => "$default_port_22",
            "internal" => "22"
        );

        foreach ($ports as $port) {
            $temp->port[] = $port['external'] . ':' . $port['internal'] . '/tcp';
        }


//        $image_id = intval($request->image);
        Input::get('image', $image_id);
        Input::get('name', $name);
        Input::get('introduce', $introduce, null, '');

        $image = ImagesModel::find(intval($image_id));
        $user = UsersModel::find($user_id);

        $name = explode('@', $user->email)[0] . '@' . $name;

        $data = [
            'name'=> $name,
            'port'=> json_encode($temp),
            'image'=> $image->name,
            'des'=> $introduce
        ];

        $docker = new \Util\DockerApi(new \Util\HttpServer());

        $res = $docker->create($data);
//        var_dump($res);exit();
        if($res) {
            $container = json_decode($res);

            Input::get('content', $content, null, '');

            $id = ContainersModel::insert(array(
                'container_id' => $container->id,
                'container_name' => $data['name'],
                'container_nickname' => $container->name,
                'image_id' => $image_id,
                'image_name' => $image->description,
                'content' => $content,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'hosts' => $container->hostIp,
                'created_at' => date('Y-m-d H:i:s'),
            ));

            foreach ($ports as $port) {
                PortsModel::insert(array(
                    'container_id' => $id,
                    'external_port' => $port['external'],
                    'internal_port' => $port['internal'],
                ));
            }

            $redis->set($key, $default_port_80);
            echo  json_encode(array(
                'status' => 0,
                'message' => '创建成功'
            ));
        }else {

            $temp_port = '';
            foreach ($ports as $port) {
                if( !empty(PortsModel::where('external_port', $port['external'])->select('*')) ) {
                    $temp_port = $port['external'];
                    break;
                }
            }

            if($temp_port) {
                echo  json_encode(array(
                    'status' => 1,
                    'message' => $temp_port . '端口被占用'
                ));
            }else {
                echo  json_encode(array(
                    'status' => 1,
                    'message' => '创建失败'
                ));
            }
        }
    }

    public function GET_imagesAction()
    {
        $images = ImagesModel::select('*');

        echo json_encode($images);
    }

    // 检测端口是否被占用
    public function detectionPortsAction()
    {
        Input::get('port', $port, null, '');
        if(!$port) {
            echo  json_encode(array(
                'status' => 1,
                'message' => '缺少参数'
            ));
            return false;
        }

        Input::get('type', $type);

        $unusable = array(80, 3000, 22);

        if(in_array($port, $unusable)) {
            if($type === 'external') {
                echo  json_encode(array(
                    'status' => 2,
                    'message' => '外部端口' . $port . '不可用'
                ));
            }else {
                echo  json_encode(array(
                    'status' => 2,
                    'message' => '内部端口' . $port . '会被默认创建'
                ));
            }
            return false;
        }

        if($type === 'external') {
            if($port < 3000) {
                echo  json_encode(array(
                    'status' => 3,
                    'message' => '端口不能小于3000'
                ));
                return false;
            }

            $max_port = 7;

            $num = PortsModel::where('external_port', $port)->count();

            if($num >= $max_port) {
                echo  json_encode(array(
                    'status' => 1,
                    'message' => $port . '端口被占用'
                ));
                return false;
            }
        }

        Input::get('cid', $cid, null, '');

        if($cid) {
            if($type === 'external') {
                if(PortsModel::where([
                    ['container_id', '=', $cid],
                    ['external_port', '=', $port],
                ])->count()) {
                    echo  json_encode(array(
                        'status' => 1,
                        'message' => '该容器已创建外部端口' . $port,
                    ));
                    return false;
                }
            }else {
                if(PortsModel::where([
                    ['container_id', '=', $cid],
                    ['internal_port', '=', $port],
                ])->count()) {
                    echo  json_encode(array(
                        'status' => 1,
                        'message' => '该容器已创建内部端口' . $port,
                    ));
                    return false;
                }
            }
        }

        echo  json_encode(array(
            'status' => 0,
            'message' => '端口可用'
        ));
        return false;
    }

    public function GET_containerOperatorAction() {
        Input::get('id', $cid);
        Input::get('type', $type);

        if( !$cid && !$type ) {
            echo json_encode(array(
                'status' => 2,
                'message' => '缺少参数'
            ));
            return false;
        }

        $docker = new \Util\DockerApi(new \Util\HttpServer());

        switch ($type) {
            case 'start':
                $res = $docker->startContainer($cid);
                $message = '开启';
                break;
            case 'restart':
                $res = $docker->restartContainer($cid);
                $message = '重启';
                break;
            case 'stop':
                $res = $docker->stopContainer($cid);
                $message = '停止';
                break;
            case 'delete':
                $res = $docker->delContainer($cid);
                $message = '删除';
                break;
            default:
                $res = '';
                $message = '';
        }

        if( !$res ) {
            echo json_encode(array(
                'status' => 1,
                'message' => $message . '失败',
            ));
            return false;
        }

        $res = json_decode($res);

        if(isset($res->status)) {
            if($res->status == 422) {
                echo json_encode(array(
                    'status' => 1,
                    'message' => '方法不是试用异常'
                ));
            }elseif($res->status == 409) {
                echo json_encode(array(
                    'status' => 1,
                    'message' => '容器不存在'
                ));
            }
            return false;
        }

        $container = ContainersModel::where(['container_id' => $cid])->find();

        switch ($type) {
            case 'restart':
            case 'start':
                $container->status = 1;
                $container->save();
                break;
            case 'stop':
                $container->status = 0;
                $container->save();
                break;
            case 'delete':
                // 删除容器以及端口
                $id = $container->id;
                PortsModel::where('container_id', $id)->delete();
                $container->delete();

        }

        echo json_encode(array(
            'status' => 0,
            'message' => $message . '成功'
        ));
    }

    public function GET_showDetailsAction($id)
    {
        Yaf_Dispatcher::getInstance()->disableView();
        $this->getView()->assign('id', $id);
        $this->display('details');
    }

    public function GET_getDetailsAction()
    {
        Input::get('id', $id);

        $container = ContainersModel::find($id);

        $ports = PortsModel::where('container_id', $container->id)->select('*');
        $mapPorts = '';
        if($ports) {
            foreach ($ports as $port) {
                if($port['internal_port'] == 3000) {
                    $terminalPort = $port['external_port'];
                }
                $mapPorts .=  '  ' . $port['external_port'] . '->' . $port['internal_port'];
            }
        }

        $container->mapPorts = $mapPorts;
        $container->ssh = array(
            'account' => 'root',
            'pass' => 'devops'
        );

        $dockerApi = new \Util\DockerApi(new \Util\HttpServer());
        $status_res = $dockerApi->statusContainer($container->container_id);
        $state = json_decode($status_res)->state;

        switch ($state) {
            case 'running': $container->state = '运行中';break;
            case 'starting': $container->state = '正在启动';break;
            case 'stopped': $container->state = '已停止';break;
            case 'sopping': $container->state = '正在停止';break;
        }

        echo json_encode($container);
    }
}