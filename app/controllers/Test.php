<?php

class TestController extends Yaf_Controller_Abstract
{
    public function indexAction(){
        Yaf_Dispatcher::getInstance()->disableView();
        //test resque
        $argus = array(
            'time' => time(),
            'array' => array(
                'test' => 'test',
            ),
        );


        $jobId =\Resque\Queue::push('Jobs\testJob',$argus,true);
        echo "Queued job ".$jobId."\n\n";
    }
}
