<?php

class WelcomeController extends Yaf_Controller_Abstract
{
    public function indexAction(){
        Yaf_Dispatcher::getInstance()->enableView();
        $this->getView();
    }
}
