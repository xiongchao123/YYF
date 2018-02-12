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
                break;
            case 'mine':
                $res = ContainersModel::where('user_id', $user_id)->page($page, $perPage)->select('*');
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

        $total = ContainersModel::count();

        echo json_encode(array(
            'total' => $total,
            'data' => $res
        ));
    }
}